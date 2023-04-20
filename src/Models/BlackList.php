<?php

namespace Aparlay\Core\Models;

use Aparlay\Core\Database\Factories\BlackListFactory;
use Aparlay\Core\Models\Enums\BlackListType;
use Aparlay\Core\Models\Queries\BlackListQueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

/**
 * Class BlackList.
 *
 * @property null $user_id
 * @property User $userObj
 */
class BlackList extends BaseModel
{
    use HasFactory;
    use Notifiable;

    /**
     * The collection associated with the model.
     *
     * @var string
     */
    protected $collection = 'black_lists';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable   = [
        '_id',
        'payload',
        'type',
        'expired_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden     = [
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts      = [
        'type' => BlackListType::class,
        'expired_at' => 'datetime',
    ];

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return BlackListFactory::new();
    }

    public static function query(): BlackListQueryBuilder|Builder
    {
        return parent::query();
    }

    public function newEloquentBuilder($query): BlackListQueryBuilder
    {
        return new BlackListQueryBuilder($query);
    }
}
