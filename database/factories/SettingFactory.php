<?php

namespace Aparlay\Core\Database\Factories;

use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Models\Setting;
use Aparlay\Core\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use MongoDB\BSON\ObjectId;

class SettingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Setting::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => 'web',
            'value' => 'waptap',
            'created_by' => function () {
                return new ObjectId(User::factory()->create()->_id);
            },
            'updated_by' => function () {
                return new ObjectId(User::factory()->create()->_id);
            },
        ];
    }
}
