<?php

namespace Aparlay\Core\Models;

use Aparlay\Core\Models\Queries\CountryQueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use MongoDB\BSON\ObjectId;

/**
 * Class Country.
 *
 * @property ObjectId   $_id
 * @property string     $name
 * @property string     $alpha2
 * @property string     $alpha3
 * @property string     $country_code
 * @property array      $flags
 * @property array      $location
 * @property string     $created_at
 * @property string     $updated_at
 */
class Country extends BaseModel
{
    /**
     * The collection associated with the model.
     *
     * @var string
     */
    protected $collection = 'countries';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        '_id',
        'name',
        'alpha2',
        'alpha3',
        'flags',
        'country_code',
        'location',
        'created_at',
        'updated_at',
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
        'is_enabled' => 'boolean',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'expired_at',
    ];

    protected $attributes = [
        'flags' => [
            '16' => '',
            '24' => '',
            '32' => '',
            '48' => '',
            '64' => '',
            '128' => '',
        ],
    ];

    /**
     * @return CountryQueryBuilder|Builder
     */
    public static function query(): CountryQueryBuilder|Builder
    {
        return parent::query();
    }

    /**
     * @param $query
     * @return CountryQueryBuilder
     */
    public function newEloquentBuilder($query): CountryQueryBuilder
    {
        return new CountryQueryBuilder($query);
    }
}
