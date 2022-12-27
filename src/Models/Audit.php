<?php

namespace Aparlay\Core\Models;

use Aparlay\Core\Casts\ObjectIdCast;
use Aparlay\Core\Helpers\Country as CountryHelper;
use Aparlay\Core\Models\Enums\MediaContentGender;
use Aparlay\Core\Models\Enums\MediaStatus;
use Aparlay\Core\Models\Enums\MediaVisibility;
use Aparlay\Core\Models\Enums\UserGender;
use Aparlay\Core\Models\Enums\UserStatus;
use Aparlay\Core\Models\Enums\UserType;
use Aparlay\Core\Models\Enums\UserVerificationStatus;
use Aparlay\Core\Models\Enums\UserVisibility;
use Illuminate\Database\Eloquent\Relations\Relation;
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

    public static function boot()
    {
        parent::boot();

        // to keep entities in database without namespace
        Relation::morphMap([
            'Media' => Media::class,
            'User' => \Aparlay\Core\Admin\Models\User::class,
            'Tip' => 'Aparlay\Payment\Models\Tip',
        ]);
    }

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
            'User' => $this->parseUserValues($values),
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
                'visibility' => ['Visibility' => MediaVisibility::from($value)->label()],
                'status' => ['Status' => MediaStatus::from($value)->label()],
                'content_gender' => ['Gender' => MediaContentGender::from($value)->label()],
                'sort_scores' => [
                    'OrSc. Default' => Arr::get($value, 'default'),
                    'OrSc. Guest' => Arr::get($value, 'guest'),
                    'OrSc. Returned' => Arr::get($value, 'returned'),
                    'OrSc. Login' => Arr::get($value, 'registered'),
                    'OrSc. paid' => Arr::get($value, 'paid'),
                ],
                'scores' => [
                    'MdSc. Skin' => Arr::get(Arr::keyBy($value, 'type'), 'skin.score'),
                    'MdSc. Beauty' => Arr::get(Arr::keyBy($value, 'type'), 'beauty.score'),
                    'MdSc. Awesomeness' => Arr::get(Arr::keyBy($value, 'type'), 'awesomeness.score'),
                ],
                'description' => ['Description' => $value],
                default => null
            };

            if (! empty($parsedValue)) {
                $parsedValues[] = $parsedValue;
            }
        }

        return $parsedValues;
    }

    private function parseUserValues(array $values): array
    {
        $parsedValues = [];
        foreach ($values as $key => $value) {
            $parsedValue = match ($key) {
                'username' => ['Username' => $value],
                'email' => ['Email' => $value],
                'email_verified' => ['Email Verif.' => empty($value) ? 'False' : 'True'],
                'bio' => ['Bio' => $value],
                'gender' => ['Gender' => UserGender::from($value)->label()],
                'status' => ['Status' => UserStatus::from($value)->label()],
                'verification_status' => ['ID Verif.' => UserVerificationStatus::from($value)->label()],
                'visibility' => ['User Verif.' => UserVisibility::from($value)->label()],
                'country_alpha2' => ['Country' => ! empty($value) ? CountryHelper::getNameByAlpha2($value) : ''],
                'payout_country_alpha2' => ['Payout Country' => ! empty($value) ? CountryHelper::getNameByAlpha2($value) : ''],
                'type' => ['Type' => UserType::from($value)->label()],
                'promo_link' => ['Prom Link' => $value],
                'referral_id' => ['Referrer' => ! empty($value) ? User::user(array_values($value)[0])->first()?->username : ''],
                default => null
            };

            if (! empty($parsedValue)) {
                $parsedValues[] = $parsedValue;
            }
        }

        return $parsedValues;
    }
}
