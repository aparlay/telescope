<?php

namespace Aparlay\Core\Tests\Browser\Admin\Email;

use Aparlay\Core\Models\Email;
use Aparlay\Core\Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Throwable;

class EmailListTest extends DuskTestCase
{
    /**
     * @test
     * @throws Throwable
     */
    public function visitEmailTest()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('core.admin.email.index'))
                ->waitFor('#datatables')
                ->assertSee('User')
                ->assertSee('Email')
                ->assertSee('Type')
                ->assertSee('Status')
                ->assertSee('Created At');
        });
    }

    /**
     * @test
     * @throws Throwable
     */
    public function searchUserTest()
    {
        $email = Email::factory()->create();

        $this->browse(function (Browser $browser) use ($email) {
            $browser->visit(route('core.admin.email.index'))
                ->clickLink('Show/Hide Filter')
                ->waitForText('User')
                ->type('user.username', $email->user['username'])
                ->pressAndWaitFor('Filter')
                ->assertSeeIn('#datatables', $email->user['username']);
        });
    }

    /**
     * @test
     * @throws Throwable
     */
    public function searchEmailTest()
    {
        $email = Email::factory()->create();

        $this->browse(function (Browser $browser) use ($email) {
            $browser->visit(route('core.admin.email.index'))
                ->clickLink('Show/Hide Filter')
                ->waitForText('Email')
                ->type('to', $email->to)
                ->pressAndWaitFor('Filter')
                ->assertSeeIn('#datatables', $email->to);
        });
    }

    /**
     * @test
     * @throws Throwable
     */
    public function searchStatusTest()
    {
        $email = Email::factory()->create();

        $this->browse(function (Browser $browser) use ($email) {
            $browser->visit(route('core.admin.email.index'))
                ->clickLink('Show/Hide Filter')
                ->waitForText('Status')
                ->select('status', $email->status)
                ->pressAndWaitFor('Filter')
                ->assertSeeIn('#datatables', $email->user['username']);
        });
    }

    /**
     * @test
     * @throws Throwable
     */
    public function searchDateRangeTest()
    {
        $email = Email::factory()->create();

        $this->browse(function (Browser $browser) use ($email) {
            $browser->visit(route('core.admin.email.index'))
                ->clickLink('Show/Hide Filter')
                ->waitForText('Date range button')
                ->press('Date range picker')
                ->clickAtXPath('/html/body/div[2]/div[1]/ul/li[1]')
                ->pressAndWaitFor('Filter')
                ->assertSeeIn('#datatables', $email->user['username']);
        });
    }
}