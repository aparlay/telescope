<?php

namespace Aparlay\Core\Models;

use Aparlay\Core\Models\Enums\MediaContentGender;
use Aparlay\Core\Models\Scopes\MediaScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Scout\Searchable;
use MongoDB\BSON\ObjectId;

/**
 * User model.
 *
 * @property ObjectId    $_id
 * @property string      $tag
 * @property int         $like_count
 * @property int         $media_count
 * @property int         $visit_count
 * @property int         $comment_count
 * @property float       $sort_score
 * @property float       $sort_score_for_male
 * @property float       $sort_score_for_female
 * @property float       $sort_score_for_transgender
 */
class Hashtag extends BaseModel
{
    use HasFactory;
    use Notifiable;
    use MediaScope;
    use Searchable;

    /**
     * The collection associated with the model.
     *
     * @var string
     */
    protected $collection = 'hashtags';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        '_id',
        'tag',
        'like_count',
        'media_count',
        'visit_count',
        'comment_count',
        'sort_score',
        'created_at',
        'updated_at',
    ];

    protected $attributes = [
        'like_count' => 0,
        'visit_count' => 0,
        'media_count' => 0,
        'comment_count' => 0,
        'sort_score' => 0,
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'tag' => 'string',
        'like_count' => 'integer',
        'visit_count' => 'integer',
        'media_count' => 'integer',
        'sort_score' => 'float',
    ];

    protected $dates = [
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
     * Get the name of the index associated with the model.
     *
     * @return string
     */
    public function searchableAs()
    {
        return 'global';
    }

    /**
     * Determine if the model should be searchable.
     *
     * @return bool
     */
    public function shouldBeSearchable(): bool
    {
        $media = Media::hashtag($this->tag)->public()->availableForFollower()->first();

        return ! empty($media);
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $media = Media::hashtag($this->tag)->public()->availableForFollower()->limit(500)->get()->random();
        $genders = Media::select(['content_gender'])
            ->hashtag($this->tag)
            ->public()
            ->availableForFollower()
            ->groupBy('content_gender')
            ->get(['content_gender'])
            ->pluck('content_gender')
            ->map(function ($gender) {
                return match ($gender) {
                    MediaContentGender::FEMALE->value => MediaContentGender::FEMALE->label(),
                    MediaContentGender::MALE->value => MediaContentGender::MALE->label(),
                    MediaContentGender::TRANSGENDER->value => MediaContentGender::TRANSGENDER->label(),
                };
            })
            ->toArray();

        return [
            '_id' => (string) $this->_id,
            'type' => 'hashtag',
            'poster' => $media?->cover_url,
            'username' => $this->tag,
            'full_name' => $this->tag,
            'description' => $this->tag,
            'hashtags' => [$this->tag],
            'score' => $this->sort_score,
            'gender' => $genders,
            'country' => '',
            'like_count' => $this->like_count,
            'visit_count' => $this->visit_count,
            'is_adult' => false,
            'skin_score' => 0,
            'last_online_at' => 0,
            'comment_count' => $this->comment_count,
            'searchable' => $this->tag,
            '_geo' => ['lat' => 0.0, 'lng' => 0.0],
        ];
    }
}
