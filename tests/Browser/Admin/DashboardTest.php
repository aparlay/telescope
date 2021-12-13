<?php

namespace Aparlay\Core\Tests\Browser\Admin;

use Aparlay\Core\Admin\Models\User;
use Aparlay\Core\Tests\DuskTestCase;
use Carbon\Carbon;
use Laravel\Dusk\Browser;
use Throwable;

class DashboardTest extends DuskTestCase
{
    /**
     * @test
     * @throws Throwable
     */
    public function analyticsFilterTest()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('core.admin.dashboard'))
                ->clickLink('Show/Hide Filter')
                ->waitForText('From Date')
                ->assertSee('From Date')
                ->assertInputPresent('from_date');
        });
    }

    /**
     * @test
     * @throws Throwable
     */
    public function analyticsFilterDateRangeErrorTest()
    {
        $dateNow = Carbon::now()->format('m/d/Y');
        $datePast = Carbon::now()->subDays(35)->format('m/d/Y');

        $this->browse(function (Browser $browser) use ($dateNow, $datePast) {
            $browser->visit(route('core.admin.dashboard'))
                ->clickLink('Show/Hide Filter')
                ->waitForText('From Date')
                ->type('from_date', $datePast)
                ->type('to_date', $dateNow)
                ->press('Filter')
                ->assertSee('Please select date range between a month');
        });
    }

    /**
     * @test
     * @throws Throwable
     */
    public function dashboardDisplayTest()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('core.admin.dashboard'))
                ->assertSee('User Analytics')
                ->assertSee('User Durations')
                ->assertSee('Media Analytics')
                ->assertSee('Media Visibility Analytics')
                ->assertSee('Email Analytics');
        });
    }
}
