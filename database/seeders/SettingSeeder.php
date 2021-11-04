<?php

namespace Aparlay\Core\Database\Seeders;

use Aparlay\Core\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        Setting::factory()->create([
            'title' => 'subscription.plans',
            'values' => [
                ''
            ]
        ]);
    }
}
