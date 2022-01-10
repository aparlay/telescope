<?php

namespace Aparlay\Core\Tests\Browser\Admin\Setting;

use Aparlay\Core\Admin\Models\Setting;
use Aparlay\Core\Tests\DuskTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Dusk\Browser;
use Throwable;

class SettingTest extends DuskTestCase
{
    use WithFaker;

    /**
     * @test
     * @throws Throwable
     */
    public function visitSettingTest()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('core.admin.setting.index'))
                ->assertSee('Group')
                ->assertSee('Title')
                ->assertSee('Value')
                ->assertSee('Created at');
        });
    }

    /**
     * @test
     * @throws Throwable
     */
    public function searchGroupTest()
    {
        $setting = Setting::factory()->create(['group' => 'group']);
        $this->browse(function (Browser $browser) use ($setting) {
            $browser->visit(route('core.admin.setting.index'))
                ->clickLink('Show/Hide Filter')
                ->waitForText('Group')
                ->type('group', $setting->group)
                ->pressAndWaitFor('Filter')
                ->assertSeeIn('#datatables', $setting->group);
        });
    }

    /**
     * @test
     * @throws Throwable
     */
    public function searchTitleTest()
    {
        $setting = Setting::factory()->create(['title' => 'name']);
        $this->browse(function (Browser $browser) use ($setting) {
            $browser->visit(route('core.admin.setting.index'))
                ->clickLink('Show/Hide Filter')
                ->waitForText('Title')
                ->type('title', $setting->title)
                ->pressAndWaitFor('Filter')
                ->assertSeeIn('#datatables', $setting->title);
        });
    }

    /**
     * @test
     * @throws Throwable
     */
    public function searchDateRangeTest()
    {
        $setting = Setting::factory()->create();

        $this->browse(function (Browser $browser) use ($setting) {
            $browser->visit(route('core.admin.setting.index'))
                ->clickLink('Show/Hide Filter')
                ->waitForText('Date range button')
                ->press('Date range picker')
                ->clickAtXPath('/html/body/div[2]/div[1]/ul/li[1]')
                ->pressAndWaitFor('Filter')
                ->assertSeeIn('#datatables', $setting->group);
        });
    }

    /**
     * @test
     * @throws Throwable
     */
    public function viewSettingTest()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('core.admin.setting.index'))
                ->waitFor('#datatables')
                ->clickAtXPath('//*[@id="datatables"]/tbody/tr[1]/td[5]/div/a')
                ->assertSee('Setting View');
        });
    }

    /**
     * @test
     * @throws Throwable
     */
    public function addSettingTest()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('core.admin.setting.index'))
                ->clickLink('Add Setting')
                ->assertSee('Add Setting')
                ->type('title', $this->faker->title)
                ->type('value', $this->faker->text(10))
                ->press('Submit')
                ->assertSee('Successfully added setting.');
        });
    }

    /**
     * @test
     *@throws Throwable
     */
    public function deleteSettingTest()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('core.admin.setting.index'))
                ->clickAtXPath('//*[@id="datatables"]/tbody/tr[1]/td[5]/div/form/a')
                ->acceptDialog()
                ->assertSee('Successfully deleted setting');
        });
    }
}
