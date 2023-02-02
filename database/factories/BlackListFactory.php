<?php

namespace Aparlay\Core\Database\Factories;

use Aparlay\Core\Models\BlackList;
use Aparlay\Core\Models\Enums\BlackListType;
use Illuminate\Database\Eloquent\Factories\Factory;

class BlackListFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BlackList::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'type' => BlackListType::TEMPORARY_EMAIL_SERVICE,
            'payload' => $this->faker->domainName(),
        ];
    }
}
