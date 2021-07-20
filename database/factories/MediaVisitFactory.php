<?php

namespace Aparlay\Core\Database\Factories;

use Aparlay\Core\Models\Media;
use Aparlay\Core\Models\MediaVisit;
use Aparlay\Core\Models\User;
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
            'user_id' => function () {
                return new ObjectId(User::factory()->create()->_id);
            },
            'media_ids' => function () {
                $ids = [];
                foreach (Media::select('_id')->inRandomOrder()->limit($this->faker->randomNumber(2))->value('_id') as $id) {
                    $ids[] = new ObjectId($id);
                }

                return $ids;
            },
            'date' => $this->faker->date(),
        ];
    }
}
