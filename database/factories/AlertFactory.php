<?php

namespace Aparlay\Core\Database\Factories;

use Aparlay\Core\Models\Alert;
use Aparlay\Core\Models\Media;
use Aparlay\Core\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AlertFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \Aparlay\Core\Models\Alert::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'reason' => $this->faker->sentence(5),
            'user_id' => function () {
                return User::factory()->create()->_id;
            },
            'media_id' => function($alert) {
                return Media::factory(['user_id' => $alert['user_id']])->create()->_id;
            },
            'type' => $this->faker->randomElement(array_keys(Alert::getTypes())),
            'status' => $this->faker->randomElement(array_keys(Alert::getTypes())),
            'created_by' => function($alert) {
                return $alert['user_id'];
            },
            'updated_by' => function($alert) {
                return $alert['user_id'];
            }
        ];
    }
}
