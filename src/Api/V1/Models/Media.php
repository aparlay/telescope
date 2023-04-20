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
 * @property int         $comment_count
 * @property array       $comments
 * @property array       $count_fields_updated_at
 * @property string      $cover
 * @property UTCDateTime $created_at
 * @property ObjectId    $created_by
 * @property array       $creator
 * @property string      $description
 * @property string      $file
 * @property mixed       $filename
 * @property string      $hash
 * @property array       $hashtags
 * @property bool        $is_comments_enabled
 * @property bool        $is_protected
 * @property int         $length
 * @property int         $like_count
 * @property array       $likes
 * @property array       $links
 * @property string      $location
 * @property string      $mime_type
 * @property array       $people
 * @property int         $size
 * @property string      $slug
 * @property int         $status
 * @property UTCDateTime $updated_at
 * @property ObjectId    $user_id
 * @property int         $visibility
 * @property-read bool $is_liked
 * @property-read int $sent_tips
 * @property-read int $skin_score
 * @property-read string $slack_admin_url
 * @property-read string $slack_subject_admin_url
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
        $message .= ' reported ' . $this->slack_subject_admin_url;
        $message .= PHP_EOL . '_*Reason:*_ ';

        return (new SlackMessage())
            ->from('Reporter', ':radioactive_sign:')
            ->to(config('app.slack_report'))
            ->content($message);
    }
}
