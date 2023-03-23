<?php

namespace Aparlay\Core\Database\Factories;

use Aparlay\Core\Models\Alert;
use Aparlay\Core\Models\Enums\AlertStatus;
use Aparlay\Core\Models\Enums\AlertType;
use Aparlay\Core\Models\Media;
use Aparlay\Core\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use MongoDB\BSON\ObjectId;

class AlertFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Alert::class;

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
                return new ObjectId(User::factory()->create()->_id);
            },
            'media_id' => function ($alert) {
                return new ObjectId(Media::factory(['user_id' => new ObjectId($alert['user_id'])])->create()->_id);
            },
            'type' => $this->faker->randomElement(AlertType::getAllValues()),
            'status' => $this->faker->randomElement(AlertStatus::getAllValues()),
            'created_by' => function ($alert) {
                return new ObjectId($alert['user_id']);
            },
            'updated_by' => function ($alert) {
                return new ObjectId($alert['user_id']);
            },
        ];
    }
}
