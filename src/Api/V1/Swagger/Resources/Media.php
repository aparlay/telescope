<?php

namespace Aparlay\Core\Api\V1\Swagger\Resources;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema()
 */
class Media
{
    /**
     * @OA\Property(property="_id", type="string", example="60237caf5e41025e1e3c80b1")
     */
    public $_id;

    /**
     * @OA\Property(property="description", type="string", example="a short description for the video file")
     */
    public $description;

    /**
     * @OA\Property(property="hash", type="string", description="Sha1 hash string of the file", example="ececbab702e0bf34e92f5370aafb8adf0fee0435")
     */
    public $hash;

    /**
     * @OA\Property(property="size", type="integer", example=3696583)
     */
    public $size;

    /**
     * @OA\Property(property="length", type="integer", example=60)
     */
    public $length;

    /**
     * @OA\Property(property="mime_type", type="string", example="video/mp4")
     */
    public $mime_type;

    /**
     * @OA\Property(property="visibility", type="integer", description="private=0, public=1", example=1)
     */
    public $visibility;

    /**
     * @OA\Property(property="status", type="integer", description="queued=0, uploaded=1, in_progress=2, completed=3, failed=4, confirmed=5, denied=6, deleted=10", example=3)
     */
    public $status;

    /**
     * @OA\Property(property="hashtags", type="array", @OA\Items (type="string", example="booboo"))
     */
    public $hashtags;

    /**
     * @OA\Property(property="people", type="array", @OA\Items (ref="#/components/schemas/SimpleUser"))
     */
    public $people;

    /**
     * @OA\Property(property="file", type="string", example="https://cdn.waptap.com/videos/60237caf5e41025e1e3c80b1.mp4")
     */
    public $file;

    /**
     * @OA\Property(property="cover", type="string", example="https://cdn.waptap.com/covers/60237caf5e41025e1e3c80b1.jpg")
     */
    public $cover;

    /**
     * @OA\Property(property="creator", ref="#/components/schemas/SimpleUser")
     */
    public $creator;

    /**
     * @OA\Property(property="is_liked", type="boolean", example=false)
     */
    public $is_liked;

    /**
     * @OA\Property(property="is_visited", type="boolean", example=true)
     */
    public $is_visited;

    /**
     * @OA\Property(property="like_count", type="number", example=24332)
     */
    public $like_count;

    /**
     * @OA\Property(property="likes", type="array", @OA\Items (ref="#/components/schemas/SimpleUser"))
     */
    public $likes;

    /**
     * @OA\Property(property="visit_count", type="number", example=432345)
     */
    public $visit_count;

    /**
     * @OA\Property(property="visits", type="array", @OA\Items (ref="#/components/schemas/SimpleUser"))
     */
    public $visits;

    /**
     * @OA\Property(property="comment_count", type="number", example=5325)
     */
    public $comment_count;

    /**
     * @OA\Property(property="comments", type="array", @OA\Items ())
     */
    public $comments;

    /**
     * @OA\Property(property="is_adult", type="boolean", example=true)
     */
    public $is_adult;

    /**
     * @OA\Property(property="is_comments_enabled", type="boolean", example=true)
     */
    public $is_comments_enabled;

    /**
     * @OA\Property(property="slug", type="string", example="weER34")
     */
    public $slug;

    /**
     * @OA\Property(property="tips", type="string", example="235400")
     */
    public $tips;

    /**
     * @OA\Property(property="tips_formatted", type="string", example="$2354.00")
     */
    public $tips_formatted;

    /**
     * @OA\Property(property="sent_tips", type="string", example="235400")
     */
    public $sent_tips;

    /**
     * @OA\Property(property="sent_tips_formatted", type="string", example="$2354.00")
     */
    public $sent_tips_formatted;

    /**
     * @OA\Property(property="created_by", type="string", example="60237caf5e41025e1e3c80b1")
     */
    public $created_by;

    /**
     * @OA\Property(property="updated_by", type="string", example=null)
     */
    public $updated_by;

    /**
     * @OA\Property(property="created_at", type="number", example=1612850111566)
     */
    public $created_at;

    /**
     * @OA\Property(property="updated_at", type="number", example=1612850111566)
     */
    public $updated_at;

    /**
     * @OA\Property(property="_links", ref="#/components/schemas/ViewLinks")
     */
    public $_links;
}
