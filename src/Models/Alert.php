<?php

namespace Aparlay\Core\Models;

use Aparlay\Core\Database\Factories\AlertFactory;
use Aparlay\Core\Models\Scopes\AlertScope;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Jenssegers\Mongodb\Eloquent\Model;

class Alert extends Model
{
    use HasFactory;
    use Notifiable;
    use AlertScope;

    public const TYPE_USER = 0;
    public const TYPE_MEDIA_REMOVED = 20;
    public const TYPE_MEDIA_NOTICED = 21;

    public const STATUS_NOT_VISITED = 0;
    public const STATUS_VISITED = 1;

    /**
     * The collection associated with the model.
     * @var string
     */
    protected $collection = 'alerts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        '_id',
        'reason',
        'user_id',
        'media_id',
        'type',
        'status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        '_id' => 'string',
        'user_id' => 'string',
        'media_id' => 'string',
        'created_by' => 'string',
        'updated_by' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * @return array
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_NOT_VISITED => __('Not Visited'),
            self::STATUS_VISITED => __('Visited'),
        ];
    }

    /**
     * @return array
     */
    public static function getTypes(): array
    {
        return [
            self::TYPE_MEDIA_NOTICED => __('Video Notice'),
            self::TYPE_MEDIA_REMOVED => __('Video Removed'),
            self::TYPE_USER => __('User'),
        ];
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return Factory
     */
    protected static function newFactory(): Factory
    {
        return AlertFactory::new();
    }
}
