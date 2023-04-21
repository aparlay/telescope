<?php

namespace Aparlay\Core\Tests\Browser\Admin;

use Aparlay\Core\Admin\Models\User;
use Aparlay\Core\Models\Enums\UserStatus;
use Aparlay\Core\Tests\DuskTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Laravel\Dusk\Browser;
use Throwable;

class LoginTest extends DuskTestCase
{
    use WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        foreach (static::$browsers as $browser) {
            $browser->driver->manage()->deleteAllCookies();
        }
    }

    /**
     * A test if admin login page is working.
     *
     * @test
     *
     * @throws Throwable
     *
     * @return void
     */
    public function visit_admin()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('core.admin.login'))
                ->assertSee('Admin Dashboard');
        });
    }

    /**
     * A test for admin login.
     *
     * @test
     *
     * @throws Throwable
     *
     * @return void
     */
    public function login_admin()
    {
        $super_admin = User::factory()->create([
            'email' => uniqid('alua_') . '@aparly.com',
            'status' => UserStatus::ACTIVE->value,
            'type' => 1,
            'password_hash' => Hash::make('password'),
            'email_verified' => true,
        ]);

        if (($super_admin = User::find($super_admin->_id)) !== null) {
            $super_admin->assignRole('super-administrator');
        }

        $this->browse(function ($browser) use ($super_admin) {
            $browser->visit(route('core.admin.login'))
                ->type('email', $super_admin->email)
                ->type('password', 'password')
                ->press('Sign In')
                ->assertPathIs('/dashboard')
                ->clickLink('Log Out')
                ->assertGuest();
        });
    }

    /**
     * @test
     *
     * @throws Throwable
     */
    public function login_incorrect_credentials()
    {
        $this->browse(function ($browser) {
            $browser->visit(route('core.admin.login'))
                ->type('email', $this->faker()->email)
                ->type('password', $this->faker()->password)
                ->press('Sign In')
                ->assertSee('The provided credentials are incorrect.');
        });
    }
}
