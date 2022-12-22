<?php

namespace Aparlay\Core\Models;

use Aparlay\Core\Casts\ObjectIdCast;
use Aparlay\Core\Models\Enums\MediaContentGender;
use Aparlay\Core\Models\Enums\MediaStatus;
use Aparlay\Core\Models\Enums\MediaVisibility;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Jenssegers\Mongodb\Eloquent\Model;
use MongoDB\BSON\UTCDateTime;

/**
 * @property array $old_values
 * @property string $parsed_old
 * @property array $new_values
 * @property string $parsed_new
 * @property UTCDateTime $created_at
 */
class Audit extends Model implements \OwenIt\Auditing\Contracts\Audit
{
    use \OwenIt\Auditing\Audit;

    /**
     * {@inheritdoc}
     */
    protected $guarded = [];

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'old_values'   => 'json',
        'new_values'   => 'json',
        'auditable_id' => ObjectIdCast::class,
        'user_id' => ObjectIdCast::class,
    ];

    /**
     * {@inheritdoc}
     */
    public function auditable()
    {
        return $this->morphTo();
    }

    /**
     * {@inheritdoc}
     */
    public function user()
    {
        return $this->morphTo();
    }

    public function getParsedOldAttribute(): string
    {
        $result = [];
        foreach (Arr::dot($this->parseValues($this->old_values)) as $title => $value) {
            $result[] = Str::substr($title, strpos($title, '.') + 1).': '.$value;
        }

        return implode("\n", $result);
    }

    public function getParsedNewAttribute(): string
    {
        $result = [];
        foreach (Arr::dot($this->parseValues($this->new_values)) as $title => $value) {
            $result[] = Str::substr($title, strpos($title, '.') + 1).': '.$value;
        }

        return implode("\n", $result);
    }

    private function parseValues(array $values): array
    {
        return match ($this->auditable_type) {
            'Media' => $this->parseMediaValues($values),
            default => $values
        };
    }

    private function parseMediaValues(array $values): array
    {
        $parsedValues = [];
        foreach ($values as $key => $value) {
            $parsedValue = match ($key) {
                'is_protected' => ['Protected' => empty($value) ? 'False' : 'True'],
                'is_comments_enabled' => ['Commentable' => empty($value) ? 'False' : 'True'],
                'is_music_licensed' => ['Music Licensed' => empty($value) ? 'False' : 'True'],
                'content_gender' => ['Content' => MediaContentGender::from($value)->label()],
                'visibility' => ['Visibility' => MediaVisibility::from($value)->label()],
                'status' => ['Stats' => MediaStatus::from($value)->label()],
                'sort_scores' => [
                    'Default' => Arr::get($value, 'default'),
                    'Guest' => Arr::get($value, 'guest'),
                    'Returned' => Arr::get($value, 'returned'),
                    'Registered' => Arr::get($value, 'registered'),
                    'paid' => Arr::get($value, 'paid'),
                ],
                'scores' => [
                    'Skin' => Arr::get(Arr::keyBy(json_decode($value, true), 'type'), 'skin.score'),
                    'Beauty' => Arr::get(Arr::keyBy(json_decode($value, true), 'type'), 'beauty.score'),
                    'Awesomeness' => Arr::get(Arr::keyBy(json_decode($value, true), 'type'), 'awesomeness.score'),
                ],
                default => null
            };

            if (! empty($parsedValue)) {
                $parsedValues[] = $parsedValue;
            }
        }

        return $parsedValues;
    }
}
