<?php

namespace Aparlay\Core\Database\Factories;

use Aparlay\Core\Models\Enums\ReportStatus;
use Aparlay\Core\Models\Enums\ReportType;
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
            'media_id' => function ($report) {
                return new ObjectId($report['media_id']);
            },
            'comment_id' => null,
            'type' => $this->faker->randomElement(ReportType::getAllValues()),
            'status' => $this->faker->randomElement(ReportStatus::getAllValues()),
            'created_by' => function ($report) {
                return new ObjectId($report['user_id']);
            },
            'updated_by' => function ($report) {
                return new ObjectId($report['user_id']);
            },
        ];
    }
}
