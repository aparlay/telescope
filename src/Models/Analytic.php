<?php

namespace Aparlay\Core\Models;

use Aparlay\Core\Database\Factories\AnalyticFactory;
use Aparlay\Core\Models\Scopes\AnalyticScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use MongoDB\BSON\UTCDateTime;

/**
 * Class Analytic.
 *
 * @property null $user_id
 * @property User $userObj
 *
 * @method static |self|Builder days(int $days)                            get days of analytics
 * @method static |self|Builder date(UTCDateTime $start, UTCDateTime $end) get analytics by date
 */
class Analytic extends BaseModel
{
    use HasFactory;
    use Notifiable;
    use AnalyticScope;

    /**
     * The collection associated with the model.
     *
     * @var string
     */
    protected $collection = 'analytics';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        '_id',
        'date',
        'media',
        'user',
        'email',
        'created_at',
        'updated_at',
        'created_by',
        'update_by',
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
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return AnalyticFactory::new();
    }

    /**
     * Get the phone associated with the user.
     */
    public function userObj()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
