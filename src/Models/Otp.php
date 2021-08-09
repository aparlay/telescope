<?php

namespace Aparlay\Core\Models;

use Aparlay\Core\Database\Factories\OrderFactory;
use Aparlay\Core\Models\Scopes\OtpScope;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class Otp extends Model
{
    use HasFactory;
    use Notifiable;
    use OtpScope;

    /**
     * The collection associated with the model.
     *
     * @var string
     */
    protected $collection = 'otps';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        '_id',
        'identity',
        'otp',
        'device_id',
        'type',
        'validated',
        'incorrect',
        'expired_at',
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
        '_id' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return OrderFactory::new();
    }
}