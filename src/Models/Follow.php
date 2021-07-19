<?php

namespace Aparlay\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Query\Builder;

class Follow extends Model
{
    use HasFactory;
    use Notifiable;

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
     * @return Builder
     */
    public static function find(): Builder
    {
        return DB::collection((new self())->collection);
    }
}
