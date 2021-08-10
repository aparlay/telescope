<?php

namespace Aparlay\Core\Database\Factories;

use Aparlay\Core\Models\Analytic;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnalyticFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Analytic::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'date' => $this->faker->dateTimeBetween('-1 month')->format('Y-m-d'),
            'media' => [
                'uploaded' => $this->faker->randomDigitNotZero(),
                'failed' => $this->faker->randomDigitNotZero(),
                'completed' => $this->faker->randomDigitNotZero(),
                'confirmed' => $this->faker->randomDigitNotZero(),
                'in_review' => $this->faker->randomDigitNotZero(),
                'deleted' => $this->faker->randomDigitNotZero(),
                'public' => $this->faker->randomDigitNotZero(),
                'private' => $this->faker->randomDigitNotZero(),
                'likes' => $this->faker->randomDigitNotZero(),
                'mean_likes' => $this->faker->randomDigitNotZero(),
                'visits' => $this->faker->randomDigitNotZero(),
                'mean_visits' => $this->faker->randomDigitNotZero(),
            ],
            'user' => [
                'registered' => $this->faker->randomDigitNotZero(),
                'login' => $this->faker->randomDigitNotZero(),
                'verified' => $this->faker->randomDigitNotZero(),
                'duration' => $this->faker->randomDigitNotZero(),
                'watched' => $this->faker->randomDigitNotZero(),
            ],
            'email' => [
                'sent' => $this->faker->randomDigitNotZero(),
                'failed' => $this->faker->randomDigitNotZero(),
                'opened' => $this->faker->randomDigitNotZero(),
            ],
        ];
    }
}
