<?php

/**
 * @OA\Schema()
 */
class Media
{
    /**
     * @OA\Property(property="_id", type="string", example="60237caf5e41025e1e3c80b1")
     * @OA\Property(property="description", type="string", example="a short description for the video file")
     * @OA\Property(property="hash", type="string", description="Sha1 hash string of the file", example="ececbab702e0bf34e92f5370aafb8adf0fee0435")
     * @OA\Property(property="size", type="integer", example=3696583)
     * @OA\Property(property="length", type="integer", example=60)
     * @OA\Property(property="mime_type", type="string", example="video/mp4")
     * @OA\Property(property="visibility", type="integer", description="private=0, public=1", example=1)
     * @OA\Property(property="status", type="integer", description="queued=0, uploaded=1, in_progress=2, completed=3, failed=4, confirmed=5, denied=6, deleted=10", example=3)
     * @OA\Property(property="hashtags", type="array", @OA\Items (type="string", example="booboo"))
     * @OA\Property(property="people", type="array", @OA\Items (ref="#/components/schemas/SimpleUser"))
     * @OA\Property(property="file", type="string", example="https://cdn.waptap.com/videos/60237caf5e41025e1e3c80b1.mp4")
     * @OA\Property(property="cover", type="string", example="https://cdn.waptap.com/covers/60237caf5e41025e1e3c80b1.jpg")
     * @OA\Property(property="creator", ref="#/components/schemas/SimpleUser")
     * @OA\Property(property="is_liked", type="boolean", example=false)
     * @OA\Property(property="is_visited", type="boolean", example=true)
     * @OA\Property(property="like_count", type="number", example=24332)
     * @OA\Property(property="likes", type="array", @OA\Items (ref="#/components/schemas/SimpleUser"))
     * @OA\Property(property="visit_count", type="number", example=432345)
     * @OA\Property(property="visits", type="array", @OA\Items (ref="#/components/schemas/SimpleUser"))
     * @OA\Property(property="comment_count", type="number", example=5325)
     * @OA\Property(property="comments", type="array", @OA\Items ())
     * @OA\Property(property="is_adult", type="boolean", example=true)
     * @OA\Property(property="slug", type="string", example="weER34")
     * @OA\Property(property="created_by", type="string", example="60237caf5e41025e1e3c80b1")
     * @OA\Property(property="updated_by", type="string", example=null)
     * @OA\Property(property="created_at", type="number", example=1612850111566)
     * @OA\Property(property="updated_at", type="number", example=1612850111566)
     * @OA\Property(property="_links", ref="#/components/schemas/ViewLinks")
     */
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
            ->to('#alua-report')
            ->content($message);
    }
}
