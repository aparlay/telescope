<?php

namespace Aparlay\Core\Tests\Browser\Admin\User;

use Aparlay\Core\Admin\Models\User;
use Aparlay\Core\Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Throwable;

class UserEditViewTest extends DuskTestCase
{
    protected $superAdminUser;

    protected $user;

    /**
     * @throws Throwable
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->superAdminUser = User::where('type', User::TYPE_ADMIN)->first();
        $this->superAdminUser->assignRole('super-administrator');

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->superAdminUser, 'admin');
        });

        $this->user = User::factory()->create();
    }

    /**
     * @test
     * @throws Throwable
     */
    public function userAlertTest()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('core.admin.user.view', ['user' => $this->user]))
                ->press('Alert')
                ->waitForText('Alert User')
                ->type('reason', 'This is a test alert.')
                ->clickAtXPath('//*[@id="alertModal"]/div/form/div/div[3]/button[2]')
                ->assertSee('Alert added successfully');
        });
    }

    /**
     * @test
     * @throws Throwable
     */
    public function userSuspendTest()
    {
        $super_admin = User::factory()->create([
            'status' => User::STATUS_ACTIVE,
        ]);

        $this->browse(function (Browser $browser) use ($super_admin) {
            $browser->visit(route('core.admin.user.view', ['user' => $super_admin->_id]))
                ->press('Suspend')
                ->waitForText('Are you sure you want to suspend this user?')
                ->clickAtXPath('//*[@id="suspendModal"]/div/div/form/div[3]/button[2]')
                ->assertSee('User Suspended successfully.');
        });
    }

    /**
     * @test
     * @throws Throwable
     */
    public function userReactivateTest()
    {
        $super_admin = User::factory()->create([
            'status' => User::STATUS_SUSPENDED,
        ]);

        $this->browse(function (Browser $browser) use ($super_admin) {
            $browser->visit(route('core.admin.user.view', ['user' => $super_admin->_id]))
                ->press('Reactivate')
                ->waitForText('Are you sure you want to reactivate this user?')
                ->clickAtXPath('//*[@id="activateModal"]/div/div/form/div[3]/button[2]')
                ->assertSee('User Reactivated successfully.');
        });
    }

    /**
     * @test
     * @throws Throwable
     */
    public function userBanTest()
    {
        $super_admin = User::factory()->create([
            'status' => User::STATUS_ACTIVE,
        ]);

        $this->browse(function (Browser $browser) use ($super_admin) {
            $browser->visit(route('core.admin.user.view', ['user' => $super_admin->_id]))
                ->press('Ban')
                ->waitForText('Are you sure you want to ban this user?')
                ->clickAtXPath('//*[@id="banModal"]/div/div/form/div[3]/button[2]')
                ->assertSee('User Banned successfully.');
        });
    }
}
