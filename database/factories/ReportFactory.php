<?php

namespace Aparlay\Core\Database\Factories;

use Aparlay\Core\Models\Report;
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
            'user_id' => function ($report) {
                return new ObjectId($report['user_id']);
            },
            'media_id' => function($report) {
                return new ObjectId($report['media_id']);
            },
            'comment_id' => null,
            'type' => $this->faker->randomElement(array_keys(Report::getTypes())),
            'status' => $this->faker->randomElement(array_keys(Report::getStatuses())),
            'created_by' => function($report) {
                return new ObjectId($report['user_id']);
            },
            'updated_by' => function($alert) {
                return new ObjectId($report['user_id']);
            }
        ];
    }
}
