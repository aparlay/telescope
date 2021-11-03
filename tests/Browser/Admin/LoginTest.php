<?php

namespace Aparlay\Core\Tests\Browser\Admin;

use Aparlay\Core\Admin\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Dusk\Browser;
use Spatie\Permission\Models\Role;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
    /**
     * A test if admin login page is working.
     *
     * @test
     * @return void
     * @throws \ThrowableN
     */
    public function visitAdmin()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                    ->screenshot('login')
                    ->assertSee('Admin Dashboard');
        });
    }

    /**
     * A test for admin login.
     *
     * @test
     * @return void
     * @throws \Throwable
     */
    public function loginAdmin()
    {
        $super_admin = User::factory()->create([
            'email' => uniqid('alua_').'@aparly.com',
            'status' => User::STATUS_ACTIVE,
            'type' => 1,
            'password_hash' => Hash::make('password'),
            'email_verified' => true,
        ]);
        $super_admin = User::find($super_admin->_id);
        $super_admin->assignRole('super-administrator');

        $this->browse(function ($browser) use ($super_admin) {
            $browser->visit('/login')
                    ->type('email', $super_admin->email)
                    ->type('password', 'password')
                    ->press('Sign In')
                    ->assertPathIs('/dashboard');
        });
    }
}
