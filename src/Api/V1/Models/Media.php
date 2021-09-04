<?php

namespace Aparlay\Core\Api\V1\Models;

use Aparlay\Core\Models\Media as MediaBase;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Redis;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

/**
 * Class Media.
 *
 * @property ObjectId    $_id
 * @property string      $description
 * @property string      $location
 * @property string      $hash
 * @property string      $file
 * @property string      $mime_type
 * @property int         $size
 * @property int         $length
 * @property int         $visibility
 * @property int         $like_count
 * @property int         $comment_count
 * @property array       $count_fields_updated_at
 * @property array       $likes
 * @property array       $comments
 * @property int         $status
 * @property array       $hashtags
 * @property array       $people
 * @property array       $creator
 * @property string      $cover
 * @property string      $slug
 * @property ObjectId    $created_by
 * @property UTCDateTime $created_at
 * @property UTCDateTime $updated_at
 * @property mixed       $filename
 * @property array       $links
 * @property bool        $is_protected
 *
 * @property-read string $slack_subject_admin_url
 * @property-read string $slack_admin_url
 * @property-read int $skin_score
 *
 * @OA\Schema()
 */
class Media extends MediaBase
{
    use Notifiable;
    use CreatorFieldTrait;

    /**
     * Get the Slack representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return SlackMessage
     */
    public function toSlack($notifiable)
    {
        $message = $this->userObj->slack_admin_url ?? 'A Guest user';
        $message .= ' reported '.$this->slack_subject_admin_url;
        $message .= PHP_EOL.'_*Reason:*_ ';

        return (new SlackMessage())
            ->from('Reporter', ':radioactive_sign:')
            ->to('#alua-report')
            ->content($message);
    }

    /**
     * Get the user's full name.
     */
    public function getIsLikedAttribute(): bool
    {
        if (auth()->guest()) {
            return false;
        }

        $mediaLikeCacheKey = (new MediaLike())->getCollection().':creator:'.auth()->user()->id;
        MediaLike::cacheByUserId(auth()->user()->id);

        return Redis::sismember($mediaLikeCacheKey, (string) $this->_id);
    }

    /**
     * Get the user's full name.
     */
    public function getIsVisitedAttribute(): bool
    {
        if (auth()->guest()) {
            return false;
        }

        $mediaVisitCacheKey = (new MediaVisit())->getCollection().':creator:'.auth()->user()->id;
        MediaLike::cacheByUserId(auth()->user()->id);

        return Redis::sismember($mediaVisitCacheKey, (string) $this->_id);
    }

    public function getFilenameAttribute(): string
    {
        return basename($this->file, '.'.pathinfo($this->file, PATHINFO_EXTENSION));
    }
}
