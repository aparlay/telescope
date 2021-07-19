<?php

namespace Aparlay\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Query\Builder;

class Alert extends Model
{
    use HasFactory;
    use Notifiable;

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
     * @return Builder
     */
    public static function find(): Builder
    {
        return DB::collection((new self())->collection);
    }
}
