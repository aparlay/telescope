<?php

namespace Aparlay\Core\Tests\Browser\Admin\Role;

use Aparlay\Core\Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Throwable;

class RoleTest extends DuskTestCase
{
    /**
     * @test
     * @throws Throwable
     */
    public function visitRoleTest()
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
     * @throws Throwable
     */
    public function viewPermissionsListTest()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('core.admin.role.index'))
                ->clickLink('Super Administrator')
                ->waitForText('Permissions')
                ->assertSee('Name')
                ->assertSee('Guard');
        });
    }

//    /**
//     * @test
//     * @throws Throwable
//     */
//    public function attachPermissionTest()
//    {
//        $this->browse(function (Browser $browser) {
//            $browser->visit(route('core.admin.role.index'))
//                ->clickAtXPath('//*[@id="accordion"]/div[7]/div[1]/div/button[1]')
//                ->waitForText('Attach Permission to Support')
//                ->select
//        });
//    }
}
