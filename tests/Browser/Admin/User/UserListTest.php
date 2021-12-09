<?php

namespace Aparlay\Core\Tests\Browser\Admin\User;

use Aparlay\Core\Admin\Models\User;
use Aparlay\Core\Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Throwable;

class UserListTest extends DuskTestCase
{
    protected $superAdminUser;

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
    }

    /**
     * @test
     * @throws Throwable
     */
    public function visitUserListingTest()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('core.admin.user.index'))
                ->waitFor('#datatables')
                ->assertSee('Username')
                ->assertSee('Email')
                ->assertSee('Fullname')
                ->assertSee('Status')
                ->assertSee('Visibility')
                ->assertSee('Followers')
                ->assertSee('Likes')
                ->assertSee('Medias')
                ->assertSee('Created at');
        });
    }

    /**
     * @test
     * @throws Throwable
     */
    public function clickViewUserTest()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('core.admin.user.index'))
                ->waitFor('#datatables')
                ->clickAtXPath('//*[@id="datatables"]/tbody/tr[1]/td[10]/a')
                ->assertSee('User Profile');
        });
    }

    /**
     * @test
     * @throws Throwable
     */
    public function searchUsernameTest()
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit(route('core.admin.user.index'))
                ->clickLink('Show/Hide Filter')
                ->waitForText('Username')
                ->type('username', $user->username)
                ->pressAndWaitFor('Filter')
                ->assertSeeIn('#datatables', $user->username);
        });
    }

    /**
     * @test
     * @throws Throwable
     */
    public function searchEmailTest()
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit(route('core.admin.user.index'))
                ->clickLink('Show/Hide Filter')
                ->waitForText('Username')
                ->type('email', $user->email)
                ->pressAndWaitFor('Filter')
                ->assertSeeIn('#datatables', $user->email);
        });
    }

    /**
     * @test
     * @throws Throwable
     */
    public function searchStatusTest()
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit(route('core.admin.user.index'))
                ->clickLink('Show/Hide Filter')
                ->waitForText('Status')
                ->select('status', $user->status)
                ->pressAndWaitFor('Filter')
                ->assertSeeIn('#datatables', $user->email);
        });
    }

    /**
     * @test
     * @throws Throwable
     */
    public function searchVisibilityTest()
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit(route('core.admin.user.index'))
                ->clickLink('Show/Hide Filter')
                ->waitForText('Visibility')
                ->select('visibility', $user->visibility)
                ->pressAndWaitFor('Filter')
                ->assertSeeIn('#datatables', $user->email);
        });
    }

    /**
     * @test
     * @throws Throwable
     */
    public function searchDateRangeTest()
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit(route('core.admin.user.index'))
                ->clickLink('Show/Hide Filter')
                ->waitForText('Date range button')
                ->press('Date range picker')
                ->clickAtXPath('/html/body/div[2]/div[1]/ul/li[1]')
                ->pressAndWaitFor('Filter')
                ->assertSeeIn('#datatables', $user->email);
        });
    }
}
