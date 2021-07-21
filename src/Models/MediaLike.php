<?php

namespace Aparlay\Core\Models;

use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Database\Factories\MediaLikeFactory;
use Aparlay\Core\Models\Scopes\MediaLikeScope;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Jenssegers\Mongodb\Eloquent\Model;
use MongoDB\BSON\ObjectId;

/**
 * Class MediaLike
 * @package Aparlay\Core\Models
 *
 * @property ObjectId $_id
 * @property string $hashtag
 * @property ObjectId $media_id
 * @property ObjectId $user_id
 * @property array $creator
 * @property string $created_at
 *
 * @property-read User $creatorObj
 * @property-read null|mixed $creator_id
 * @property-read Media $media
 * @property-read User $userObj
 */
class MediaLike extends Model
{
    use HasFactory;
    use Notifiable;
    use MediaLikeScope;

    /**
     * The collection associated with the model.
     * @var string
     */
    protected $collection = 'media_visits';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        '_id',
        'media_id',
        'user_id',
        'creator',
        'created_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        '_id' => 'string',
        'media_id' => 'string',
        'user_id' => 'string',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
        'deleted_at' => 'timestamp',
    ];

    /**
     * Get the user's full name.
     *
     * @return string
     */
    public function getCreatorAttribute($creator)
    {
        $creator['_id'] = (string)$creator['_id'];

        if (auth()->guest()) {
            $creator['is_followed'] = false;
            $creator['is_liked'] = false;

            return $creator;
        }
        $user = auth()->user();
        $creator['is_followed'] = isset($this->creator['_id'], $user->following[(string)$this->creator['_id']]);

        return $creator;
    }

    /**
     * Set the mediaLike's creator.
     *
     * @return array
     */
    public function setCreatorAttribute($creator)
    {
        $creator = User::user($creator['_id'])->first();
        return ['_id' => $creator->_id, 'username' => $creator->username, 'avatar' => $creator->avatar];
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return Factory
     */
    protected static function newFactory(): Factory
    {
        return MediaLikeFactory::new();
    }
}
