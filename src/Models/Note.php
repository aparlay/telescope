<?php

namespace Aparlay\Core\Models;

use Aparlay\Core\Database\Factories\NoteFactory;
use Aparlay\Core\Models\Enums\NoteType;
use Aparlay\Core\Models\Scopes\NoteScope;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Note extends BaseModel
{
    use HasFactory;
    use Notifiable;
    use NoteScope;
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
        ];
    }

}
