<?php

namespace Aparlay\Core\Database\Factories;

use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Models\User;
use Aparlay\Core\Models\Otp;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use MongoDB\BSON\ObjectId;

class OtpFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Otp::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'identity' => $this->faker->unique()->safeEmail(),
            'otp' => (string) $this->faker->randomNumber(6),
            'type' => 'email',
            'device_id' => Str::random(20),
            'incorrect' => 0,
            'validated' => $this->faker->boolean(),
            'created_by' => function () {
                return new ObjectId(User::factory()->create()->_id);
            },
            'updated_by' => function () {
                return new ObjectId(User::factory()->create()->_id);
            }
        ];
    }
}
