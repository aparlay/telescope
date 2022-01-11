<?php

namespace Aparlay\Core\Models;

use Aparlay\Core\Database\Factories\OtpFactory;
use Aparlay\Core\Models\Scopes\OtpScope;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

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
}
