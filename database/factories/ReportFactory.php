<?php

namespace Aparlay\Core\Database\Factories;

use Aparlay\Core\Models\Media;
use Aparlay\Core\Models\Report;
use Aparlay\Core\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use MongoDB\BSON\ObjectId;

class ReportFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Report::class;

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
                return Media::factory(['user_id' => new ObjectId($alert['user_id'])])->create()->_id;
            },
            'comment_id' => null,
            'type' => $this->faker->randomElement(array_keys(Report::getTypes())),
            'status' => $this->faker->randomElement(array_keys(Report::getStatuses())),
            'created_by' => function($alert) {
                return new ObjectId($alert['user_id']);
            },
            'updated_by' => function($alert) {
                return new ObjectId($alert['user_id']);
            }
        ];
    }
}
