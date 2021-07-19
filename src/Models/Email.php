<?php

namespace Aparlay\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Jenssegers\Mongodb\Eloquent\Model;

class Email extends Model
{
    use HasFactory;
    use Notifiable;

    public const STATUS_QUEUED = 0;
    public const STATUS_SENT = 1;
    public const STATUS_OPENED = 2;
    public const STATUS_FAILED = 3;

    public const TYPE_OTP = 0;

    /**
     * The collection associated with the model.
     * @var string
     */
    protected $collection = 'emails';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        '_id',
        'user',
        'to',
        'status',
        'type',
        'tracking',
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
    ];
}
