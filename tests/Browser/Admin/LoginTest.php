<?php

namespace Aparlay\Core\Tests\Browser\Admin;

use Aparlay\Core\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
    /**
     * A test if admin login page is working.
     *
     * @test
     * @return void
     * @throws \Throwable
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
//        $active_admin = User::factory()->create([
//            'email' => uniqid('alua_').'@aparly.com',
//            'status' => User::STATUS_ACTIVE,
//            'type' => 1,
//        ]);
//
//        $this->browse(function ($browser) use ($active_admin) {
//            $browser->visit('/login')
//                    ->type('email', $active_admin->email)
//                    ->type('password', 'password')
//                    ->press('Sign In')
//                    ->assertPathIs('/dashboard');
//        });
    }
}
