<?php

namespace Aparlay\Core\Models;

use Aparlay\Core\Models\Scopes\MediaLikeScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Query\Builder;

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
