<?php

namespace Aparlay\Core\Api\V1\Models;

use Aparlay\Core\Api\V1\Models\Scopes\MediaScope;
use Aparlay\Core\Models\Media as MediaBase;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notifiable;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

/**
 * Class Media.
 *
 * @property ObjectId    $_id
 * @property ObjectId    $user_id
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
 * @property bool        $is_comments_enabled
 *
 *
 * @property-read string $slack_subject_admin_url
 * @property-read string $slack_admin_url
 * @property-read int $skin_score
 * @property-read bool $is_liked
 * @property-read int $sent_tips
 */
class Media extends MediaBase
{
    use Notifiable;
    use CreatorFieldTrait;

    protected static function booted()
    {
        static::addGlobalScope(new MediaScope());
    }

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
            ->to(config('app.slack_report'))
            ->content($message);
    }
}
