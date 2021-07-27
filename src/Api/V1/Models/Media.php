<?php

namespace Aparlay\Core\Api\V1\Models;

use Aparlay\Core\Models\Media as MediaBase;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

/**
 * Class Media
 *
 * @package Aparlay\Core\Api\V1\Models
 * @property ObjectId $_id
 * @property string $description
 * @property string $location
 * @property string $hash
 * @property string $file
 * @property string $mime_type
 * @property int $size
 * @property int $length
 * @property int $visibility
 * @property int $like_count
 * @property int $comment_count
 * @property array $count_fields_updated_at
 * @property array $likes
 * @property array $comments
 * @property int $status
 * @property array $hashtags
 * @property array $people
 * @property array $creator
 * @property string $cover
 * @property string $slug
 * @property ObjectId $created_by
 * @property UTCDateTime $created_at
 * @property UTCDateTime $updated_at
 * @property-read mixed $filename
 * @property-read array $links
 * @property-read bool $is_protected
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
     * @param  mixed  $notifiable
     * @return SlackMessage
     */
    public function toSlack($notifiable)
    {
        $message = $this->creator->slack_admin_url ?? 'A Guest user';
        $message .= ' reported ' . $this->slack_subject_admin_url;
        $message .= PHP_EOL . '_*Reason:*_ ' . $this->reason;

        return (new SlackMessage())
            ->from('Reporter', ':radioactive_sign:')
            ->to('#alua-report')
            ->content($message);
    }

    /**
     * Get the user's full name.
     *
     * @return bool
     */
    public function getIsLikedAttribute(): bool
    {
        if (auth()->guest()) {
            return false;
        }

        $mediaLikeCacheKey = 'MediaLike.creator.' . auth()->user()->id;
        $mediaLike = Cache::remember($mediaLikeCacheKey, 'cache.longDuration', function () {
            return MediaLike::select(['media_id' => 1, '_id' => 0])->creator(auth()->user()->id)->pluck('media_id');
        });

        return isset($mediaLike[(string)$this->_id]);
    }

    /**
     * Get the user's full name.
     *
     * @return bool
     */
    public function getIsVisitedAttribute(): bool
    {
        if (auth()->guest()) {
            return false;
        }

        $mediaLikeCacheKey = 'MediaVisit.creator.' . auth()->user()->id;
        $mediaLike = Cache::remember($mediaLikeCacheKey, 'cache.longDuration', function () {
            return MediaVisit::select(['media_id' => 1, '_id' => 0])->user(auth()->user()->id)->pluck('media_id');
        });

        return isset($mediaLike[(string)$this->_id]);
    }
}
