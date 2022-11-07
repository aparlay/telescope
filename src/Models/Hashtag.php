<?php

namespace Aparlay\Core\Models;

use Aparlay\Core\Models\Enums\UserInterestedIn;
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
        'sort_score_for_male',
        'sort_score_for_female',
        'sort_score_for_transgender',
        'created_at',
        'updated_at',
    ];

    protected $attributes = [
        'like_count' => 0,
        'visit_count' => 0,
        'media_count' => 0,
        'comment_count' => 0,
        'sort_score' => 0,
        'sort_score_for_male' => 0,
        'sort_score_for_female' => 0,
        'sort_score_for_transgender' => 0,
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
        'sort_score_for_male' => 'float',
        'sort_score_for_female' => 'float',
        'sort_score_for_transgender' => 'float',
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
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $media = Media::hashtag($this->tag)->sort('default')->first();

        return [
            '_id' => (string) $this->_id,
            'type' => 'hashtag',
            'poster' => $media?->cover_url,
            'username' => $this->tag,
            'full_name' => $this->tag,
            'description' => $this->tag,
            'hashtags' => [$this->tag],
            'score' => $this->sort_score,
            'score_for_male' => $this->sort_score_for_male,
            'score_for_female' => $this->sort_score_for_female,
            'score_for_transgender' => $this->sort_score_for_transgender,
            'gender' => [],
            'country' => '',
            'like_count' => $this->like_count,
            'visit_count' => $this->visit_count,
            'comment_count' => $this->comment_count,
            'last_online_at' => 0,
            '_geo' => ['lat' => 0.0, 'lng' => 0.0],
        ];
    }

    public function recalculateScores()
    {
        $count = Media::hashtag($this->tag)->count();
        $this->sort_score = (Media::hashtag($this->tag)->sum('sort_scores.default') / $count);

        $queryForMale = Media::hashtag($this->tag)->contentGender([UserInterestedIn::MALE->value]);
        $countForMale = $queryForMale->count();
        $this->sort_score_for_male = ($countForMale > 0) ? ($queryForMale->sum('sort_scores.default') / $countForMale) : 0;

        $queryForFemale = Media::hashtag($this->tag)->contentGender([UserInterestedIn::FEMALE->value]);
        $countForFemale = $queryForFemale->count();
        $this->sort_score_for_female = ($countForFemale > 0) ? ($queryForFemale->sum('sort_scores.default') / $countForFemale) : 0;

        $queryForTrans = Media::hashtag($this->tag)->contentGender([UserInterestedIn::TRANSGENDER->value]);
        $countForTrans = $queryForTrans->count();
        $this->sort_score_for_transgender = ($countForTrans > 0) ? ($queryForTrans->sum('sort_scores.default') / $countForTrans) : 0;
        $this->save();
        $this->refresh();
    }
}
