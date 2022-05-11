<?php

namespace Aparlay\Core\Models\Traits;

use Aparlay\Core\Helpers\Country as CountryHelper;

trait CountryFields
{
    public function getPayoutCountryLabelAttribute()
    {
        return $this->payout_country_alpha2 ? CountryHelper::getNameByAlpha2($this->payout_country_alpha2) : '';
    }

    public function getCountryLabelAttribute()
    {
        return $this->country_alpha2 ? CountryHelper::getNameByAlpha2($this->country_alpha2) : '';
    }

    public function getCountryFlagAttribute()
    {
        return $this->country_alpha2 ? CountryHelper::getFlagByAlpha2($this->country_alpha2) : '';
    }

    public function getCountryFlagsAttribute()
    {
        return $this->country_alpha2 ? [
            '16' => CountryHelper::getFlagByAlpha2($this->country_alpha2, '16'),
            '24' => CountryHelper::getFlagByAlpha2($this->country_alpha2, '24'),
            '32' => CountryHelper::getFlagByAlpha2($this->country_alpha2, '32'),
            '48' => CountryHelper::getFlagByAlpha2($this->country_alpha2, '48'),
            '64' => CountryHelper::getFlagByAlpha2($this->country_alpha2, '64'),
            '128' => CountryHelper::getFlagByAlpha2($this->country_alpha2, '128'),
        ] : [
            '16' => '',
            '24' => '',
            '32' => '',
            '48' => '',
            '64' => '',
            '128' => '',
        ];
    }
}
