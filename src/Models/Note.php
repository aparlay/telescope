<?php

namespace Aparlay\Core\Models;

use Aparlay\Core\Database\Factories\NoteFactory;
use Aparlay\Core\Models\Enums\NoteType;
use Aparlay\Core\Models\Enums\NoteCategory;
use Aparlay\Core\Models\Queries\NoteQueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Jenssegers\Mongodb\Relations\BelongsTo;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

/**
 * Class Note.
 *
 * @property ObjectId           $_id
 * @property ObjectId           $created_by
 * @property ObjectId           $updated_by
 * @property UTCDateTime        $created_at
 * @property UTCDateTime        $updated_at
 * @property int                $type
 * @property int                $category
 * @property int                $status
 * @property array              $creator
 * @property array              $user
 * @property string             $message
 *
 * @property User               $userObj
 * @property User               $creatorObj
 */
class Note extends BaseModel
{
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    /**
     * The collection associated with the model.
     *
     * @var string
     */
    protected $collection = 'notes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        '_id',
        'type',
        'category',
        'message',
        'user',
        'creator',
        'status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    /**
     * Get the creator associated with the follow.
     */
    public function creatorObj(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator._id');
    }

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return NoteFactory::new();
    }

    /**
     * @return NoteQueryBuilder|Builder
     */
    public static function query(): NoteQueryBuilder|Builder
    {
        return parent::query();
    }

    /**
     * @param $query
     *
     * @return NoteQueryBuilder
     */
    public function newEloquentBuilder($query): NoteQueryBuilder
    {
        return new NoteQueryBuilder($query);
    }

    /**
     * @return array
     */
    public static function getTypes(): array
    {
        return [
            NoteType::SUSPEND->value => NoteType::SUSPEND->label(),
            NoteType::UNSUSPEND->value => NoteType::UNSUSPEND->label(),
            NoteType::BAN->value => NoteType::BAN->label(),
            NoteType::UNBAN->value => NoteType::UNBAN->label(),
            NoteType::WARNING_MESSAGE->value => NoteType::WARNING_MESSAGE->label(),
            NoteType::BAN_ALL_CC_PAYMENT->value => NoteType::BAN_ALL_CC_PAYMENT->label(),
            NoteType::UNBAN_ALL_CC_PAYMENT->value => NoteType::UNBAN_ALL_CC_PAYMENT->label(),
            NoteType::OTHER->value => NoteType::OTHER->label(),
        ];
    }

    /**
     * @return array
     */
    public static function getCategories(): array
    {
        return [
            NoteCategory::LOG->value => NoteCategory::LOG->label(),
            NoteCategory::NOTE->value => NoteCategory::NOTE->label(),
        ];
    }
}
