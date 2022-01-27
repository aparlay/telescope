<?php

namespace Aparlay\Core\Models;

use Aparlay\Core\Database\Factories\SettingFactory;
use Aparlay\Core\Models\Scopes\VersionScope;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class Setting extends BaseModel
{
    use HasFactory;
    use Notifiable;
    use VersionScope;

    /**
     * The collection associated with the model.
     *
     * @var string
     */
    protected $collection = 'settings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        '_id',
        'group',
        'title',
        'value',
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
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return SettingFactory::new();
    }

    /**
     * @return string
     */
    public function getAdminUrlAttribute(): string
    {
        return route('core.admin.setting.view', ['setting' => $this->_id]);
    }
}
