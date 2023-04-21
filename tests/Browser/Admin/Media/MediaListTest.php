<?php

namespace Aparlay\Core\Tests\Browser\Admin\Media;

use Aparlay\Core\Admin\Models\Media;
use Aparlay\Core\Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Throwable;

class MediaListTest extends DuskTestCase
{
    /**
     * @test
     *
     * @throws Throwable
     */
    public function visit_media_listing_test()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('core.admin.media.index'))
                ->waitFor('#datatables')
                ->assertSee('Cover')
                ->assertSee('Created By')
                ->assertSee('Description')
                ->assertSee('Status')
                ->assertSee('Likes')
                ->assertSee('Visits')
                ->assertSee('Sort Score')
                ->assertSee('Created At');
        });
    }

    /**
     * @test
     *
     * @throws Throwable
     */
    public function click_view_media_test()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('core.admin.media.index'))
                ->waitFor('#datatables')
                ->clickAtXPath('//*[@id="datatables"]/tbody/tr[1]/td[9]/a')
                ->assertSee('Media View');
        });
    }

    /**
     * @test
     *
     * @throws Throwable
     */
    public function search_created_by_test()
    {
        $media = Media::factory()->create();

        $this->browse(function (Browser $browser) use ($media) {
            $browser->visit(route('core.admin.media.index'))
                ->clickLink('Show/Hide Filter')
                ->waitForText('Created By')
                ->type('creator.username', $media->creator['username'])
                ->pressAndWaitFor('Filter')
                ->assertSeeIn('#datatables', $media->creator['username']);
        });
    }

    /**
     * @test
     *
     * @throws Throwable
     */
    public function search_status_test()
    {
        $media = Media::factory()->create();

        $this->browse(function (Browser $browser) use ($media) {
            $browser->visit(route('core.admin.media.index'))
                ->clickLink('Show/Hide Filter')
                ->waitForText('Status')
                ->select('status', $media->status)
                ->pressAndWaitFor('Filter')
                ->assertSeeIn('#datatables', $media->creator['username']);
        });
    }

    /**
     * @test
     *
     * @throws Throwable
     */
    public function search_date_range_test()
    {
        $media = Media::factory()->create();

        $this->browse(function (Browser $browser) use ($media) {
            $browser->visit(route('core.admin.media.index'))
                ->clickLink('Show/Hide Filter')
                ->waitForText('Date range button')
                ->press('Date range picker')
                ->clickAtXPath('/html/body/div[2]/div[1]/ul/li[1]')
                ->pressAndWaitFor('Filter')
                ->assertSeeIn('#datatables', $media->creator['username']);
        });
    }
}
