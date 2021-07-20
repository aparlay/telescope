<?php

namespace Aparlay\Core\Models;

use Aparlay\Core\Models\Scopes\FollowScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Jenssegers\Mongodb\Eloquent\Model;

class Follow extends Model
{
    use HasFactory;
    use Notifiable;
    use FollowScope;

    public const STATUS_PENDING = 0;
    public const STATUS_ACCEPTED = 1;

    /**
     * The collection associated with the model.
     * @var string
     */
    protected $collection = 'user_follows';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        '_id',
        'user',
        'creator',
        'is_deleted',
        'status',
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
        'created_at' => 'datetime',
    ];

    /**
     * @return array
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING => __('Pending'),
            self::STATUS_ACCEPTED => __('Accepted'),
        ];
    }
}
