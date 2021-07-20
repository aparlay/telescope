<?php

namespace Aparlay\Core\Database\Factories;

use Aparlay\Core\Models\Version;
use Illuminate\Database\Eloquent\Factories\Factory;

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
            'os' => $this->faker->sys,
            'app',
            'version',
            'is_force_update',
            'expired_at',
            'created_by',
            'updated_by',
            'created_at',
            'updated_at',
        ];
    }
}
