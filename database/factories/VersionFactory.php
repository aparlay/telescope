<?php

namespace Aparlay\Core\Database\Factories;

use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Models\User;
use Aparlay\Core\Models\Version;
use Illuminate\Database\Eloquent\Factories\Factory;
use MongoDB\BSON\ObjectId;

class VersionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Version::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'os' => 'web',
            'app' => 'waptap',
            'version' => $this->faker->randomNumber(1),
            'is_force_update' => $this->faker->boolean,
            'expired_at' => DT::utcNow(),
            'created_by' => function() {
                return new ObjectId(User::factory()->create()->_id);
            },
            'updated_by' => function() {
                return new ObjectId(User::factory()->create()->_id);
            }
        ];
    }
}
