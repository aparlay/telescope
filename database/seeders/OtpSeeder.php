<?php

namespace Aparlay\Core\Database\Seeders;

use Aparlay\Core\Models\Otp;
use Aparlay\Core\Models\User;
use Illuminate\Database\Seeder;

class OtpSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Otp::factory()
            ->count(20)
            ->for(User::factory()->create(), 'userObj')
            ->create();
    }
}
