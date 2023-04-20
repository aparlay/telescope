<?php

namespace Aparlay\Core\Models;

use Aparlay\Core\Database\Factories\ActiveUserFactory;
use Aparlay\Core\Models\Queries\ActiveUserQueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Analytic.
 *
 * @property null $user_id
 * @property User $userObj
 */
class ActiveUser extends BaseModel
{
    use HasFactory;

    /**
     * The collection associated with the model.
     *
     * @var string
     */
    protected $collection = 'active_user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable   = [
        '_id',
        'date',
        'uuid',
        'created_at',
        'updated_at',
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
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return ActiveUserFactory::new();
    }

    public static function query(): ActiveUserQueryBuilder|Builder
    {
        return parent::query();
    }

    public function newEloquentBuilder($query): ActiveUserQueryBuilder
    {
        return new ActiveUserQueryBuilder($query);
    }
}
