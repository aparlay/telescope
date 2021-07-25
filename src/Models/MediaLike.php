<?php

namespace Aparlay\Core\Models;

use Aparlay\Core\Database\Factories\MediaLikeFactory;
use Aparlay\Core\Models\Scopes\MediaLikeScope;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
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
    protected $collection = 'media_likes';

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
        'created_by',
        'updated_by',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [];

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
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
        'deleted_at' => 'timestamp',
    ];

    /**
     * Get the phone associated with the user.
     */
    public function userObj()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the phone associated with the user.
     */
    public function mediaObj()
    {
        return $this->belongsTo(Media::class, 'media_id');
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
