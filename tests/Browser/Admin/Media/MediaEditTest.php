<?php

namespace Aparlay\Core\Tests\Browser\Admin\Media;

use Aparlay\Core\Admin\Models\Media;
use Aparlay\Core\Tests\DuskTestCase;
use Facebook\WebDriver\WebDriverBy;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Laravel\Dusk\Browser;
use Throwable;

class MediaEditTest extends DuskTestCase
{
    use WithFaker;

    protected $media;

    /**
     * @throws Throwable
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->media = Media::factory()->create();
    }

    /**
     * @test
     */
    public function mediaReprocessTest()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('core.admin.media.view', ['media' => $this->media]))
                ->press('Reprocess')
                ->waitForText('Are you sure you want to reprocess this item?')
                ->press('Ok')
                ->assertSee('Video is placed in queue for reprocessing.');
        });
    }

    /**
     * @test
     */
    public function mediaAlertTest()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('core.admin.media.view', ['media' => $this->media]))
                ->click('#mediaAlert')
                ->waitForText('Media Alert')
                ->value('#alert-modal-form-reason', 'This is a test alert.')
                ->clickAtXPath('//*[@id="alert-modal"]/div/div/div[3]/button[2]')
                ->assertSee('Alert added successfully.');
        });
    }

    /**
     * @test
     */
    public function mediaDeleteAlertTest()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('core.admin.media.view', ['media' => $this->media]))
                ->click('#deleteAlert')
                ->waitForText('Media Alert')
                ->type('reason', 'This is a test delete and alert.')
                ->clickAtXPath('//*[@id="delete-alert-modal"]/div/div/div[3]/button[2]')
                ->assertSee('Alert added successfully.');
        });
    }

    /**
     * @test
     */
    public function mediaDeniedTest()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('core.admin.media.view', ['media' => $this->media]))
                ->press('Denied')
                ->assertSee('Media updated successfully');
        });
    }

    /**
     * @test
     */
    public function mediaEditTest()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('core.admin.media.view', ['media' => $this->media]))
                ->type('description', $this->faker()->text)
                ->select('status')
                ->clickAtXPath('//*[@id="media-info"]/form/div[13]/div/button')
                ->assertSee('Media updated successfully');
        });
    }

    /**
     * @test
     */
    public function mediaSkinAwesomenessTest()
    {
        $randomScore = rand(0, 10);
        $this->browse(function (Browser $browser) use ($randomScore) {
            $browser->visit(route('core.admin.media.view', ['media' => $this->media]))
                ->clickAtXPath('//*[@id="skin_score_1"]/label')
                ->clickAtXPath('//*[@id="awesomeness_score_1"]/label');
            if ($browser->driver->findElement(WebDriverBy::cssSelector('#mediaSave'))->isDisplayed()) {
                $browser->press('Save')
                    ->assertSee('Media updated successfully');
            } else {
                $browser->press('Approve')
                    ->assertSee('Media updated successfully');
            }
        });
    }

    /**
     * @test
     * @throws Throwable
     */
    public function mediaUploadTest()
    {
        $file = UploadedFile::fake()->create('random.mp4')->store('public/dusk/medias');

        $this->browse(function (Browser $browser) use ($file) {
            $browser->visit(route('core.admin.media.view', ['media' => $this->media]))
                ->clickLink('Upload')
                ->attach('.flow-browse input', storage_path('app/'.$file))
                ->assertSee('Uploading')
                ->assertSee('completed')
                ->click('.upload-video-button')
                ->assertSee('Video uploaded successfully');
        });
    }
}