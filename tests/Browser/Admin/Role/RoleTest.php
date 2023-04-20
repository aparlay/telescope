<?php

namespace Aparlay\Core\Tests\Browser\Admin\Role;

use Aparlay\Core\Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Throwable;

class RoleTest extends DuskTestCase
{
    /**
     * @test
     *
     * @throws Throwable
     */
    public function visit_role_test()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('core.admin.role.index'))
                ->assertSee('Super Administrator')
                ->assertSee('Administrator')
                ->assertSee('Support');
        });
    }

    /**
     * @test
     *
     * @throws Throwable
     */
    public function view_permissions_list_test()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('core.admin.role.index'))
                ->clickLink('Super Administrator')
                ->waitForText('Permissions')
                ->assertSee('Name')
                ->assertSee('Guard');
        });
    }

    /**
     * @test
     *
     * @throws Throwable
     */
    public function attach_permission_test()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('core.admin.role.index'))
                ->clickAtXPath('//*[@id="accordion"]/div[7]/div[1]/div/button[1]')
                ->waitForText('Attach Permission to Support')
                ->clickAtXPath('//*[@id="attach2"]/div/div/form/div[2]/div/div/div/span/span[1]/span/ul/li/input')
                ->click('.select2-results li:first-child')
                ->clickAtXPath('//*[@id="attach2"]/div/div/form/div[3]/button[2]')
                ->assertSee('Successfully updated role.');
        });
    }

    /**
     * @test
     *
     * @throws Throwable
     */
    public function remove_permission_test()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('core.admin.role.index'))
                ->clickAtXPath('//*[@id="accordion"]/div[7]/div[1]/div/button[2]')
                ->waitForText('Remove Permission to Support')
                ->clickAtXPath('//*[@id="remove2"]/div/div/form/div[2]/div/div/div/span/span[1]/span/ul/li/input')
                ->click('.select2-results li:first-child')
                ->clickAtXPath('//*[@id="remove2"]/div/div/form/div[3]/button[2]')
                ->assertSee('Successfully updated role.');
        });
    }
}
