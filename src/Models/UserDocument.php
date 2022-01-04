<?php

namespace Aparlay\Core\Models;

use Aparlay\Core\Api\V1\Traits\HasFileTrait;
use Aparlay\Core\Casts\SimpleUserCast;
use Aparlay\Core\Constants\StorageType;
use Aparlay\Core\Database\Factories\UserDocumentFactory;
use Aparlay\Core\Models\Enums\UserDocumentStatus;
use Aparlay\Core\Models\Enums\UserDocumentType;
use Aparlay\Core\Models\Scopes\UserDocumentScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Jenssegers\Mongodb\Relations\BelongsTo;
use MongoDB\BSON\ObjectId;

/**
 * Class UserDocument.
 *
 * @property ObjectId $_id
 * @property ObjectId $user_id
 * @property int $type
 * @property int $status
 * @property string $md5
 * @property string $file
 * @property string $size
 * @property string $mime
 * @property array $creator
 * @property User $creatorObj
 * @property User $userObj
 * @property ObjectId $created_by
 * @property ObjectId $updated_by
 * @property string $created_at
 * @property string $updated_at
 *
 * @method static |self|Builder user(ObjectId|string $userId)              get user who liked media
 * @method static |self|Builder creator(ObjectId|string $creatorId)        get creator user who liked media
 */
class UserDocument extends BaseModel
{
    use HasFactory;
    use Notifiable;
    use UserDocumentScope;
    use HasFileTrait;

    /**
     * The collection associated with the model.
     *
     * @var string
     */
    protected $collection = 'user_documents';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        '_id',
        'type',
        'status',
        'md5',
        'file',
        'size',
        'mime',
        'creator',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'status' => 'integer',
        'type' => 'integer',
        'creator' => SimpleUserCast::class.':_id,username,avatar',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return UserDocumentFactory::new();
    }

    /**
     * Get the creator associated with the follow.
     */
    public function creatorObj(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator._id');
    }

    public function getFilePath()
    {
        if ($this->creatorObj) {
            return $this->creatorObj->_id.'/'.$this->file;
        }

        return null;
    }

    /**
     * @return string
     */
    public function getStorageDisk()
    {
        return StorageType::B2_DOCUMENTS;
    }

    public function getStatusLabelAttribute()
    {
        return UserDocumentStatus::from($this->status)->label();
    }

    public function getTypeLabelAttribute()
    {
        return UserDocumentType::from($this->type)->label();
    }
}
