<?php

namespace Aparlay\Core\Database\Factories;

use Aparlay\Core\Models\Media;
use Aparlay\Core\Models\MediaVisit;
use Illuminate\Database\Eloquent\Factories\Factory;
use MongoDB\BSON\ObjectId;

class MediaVisitFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = MediaVisit::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => function ($model) {
                return new ObjectId($model['user_id']);
            },
            'media_ids' => function () {
                $ids = [];
                foreach (Media::select('_id')->limit($this->faker->randomNumber(2))->pluck('_id') as $id) {
                    $ids[] = new ObjectId($id);
                }

                return $ids;
            },
            'date' => $this->faker->date(),
            'created_by' => function ($model) {
                return new ObjectId($model['user_id']);
            },
            'updated_by' => function ($model) {
                return new ObjectId($model['user_id']);
            },
        ];
    }
}
