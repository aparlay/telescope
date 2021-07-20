<?php

namespace Aparlay\Core\Models;

use Aparlay\Core\Database\Factories\VersionFactory;
use Aparlay\Core\Models\Scopes\VersionScope;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Jenssegers\Mongodb\Eloquent\Model;

class Version extends Model
{
    use HasFactory;
    use Notifiable;
    use VersionScope;

    public const OS_ANDROID = 'android';
    public const OS_IOS = 'ios';

    /**
     * The collection associated with the model.
     * @var string
     */
    protected $collection = 'versions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        '_id',
        'os',
        'app',
        'version',
        'is_force_update',
        'expired_at',
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
     *
     * @return Factory
     */
    protected static function newFactory(): Factory
    {
        return VersionFactory::new();
    }
}
