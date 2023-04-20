<?php

namespace Aparlay\Core\Database\Factories;

use Aparlay\Core\Models\ActiveUser;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActiveUserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ActiveUser::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'date' => $this->faker->dateTimeBetween('-1 month')->format('Y-m-d'),
            'uuid' => $this->faker->uuid,
        ];
    }
}
