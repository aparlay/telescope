<?php

namespace Aparlay\Core\Models;

use Aparlay\Core\Casts\ObjectIdCast;
use Jenssegers\Mongodb\Eloquent\Model;

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
        return is_array($this->old_values) ? implode(PHP_EOL, $this->parseValues($this->old_values)) : $this->old_values;
    }

    public function getParsedNewAttribute(): string
    {
        return is_array($this->new_values) ? implode(PHP_EOL, $this->parseValues($this->new_values)) : $this->new_values;
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
                default => null
            };

            if (!empty($parsedValue)) {
                $parsedValues[] = $parsedValue;
            }
        }

        return $parsedValues;
    }
}
