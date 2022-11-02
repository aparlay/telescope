<?php

namespace Aparlay\Core\Tests\Browser\Admin\User;

use Aparlay\Core\Admin\Models\User;
use Aparlay\Core\Models\Enums\UserStatus;
use Aparlay\Core\Tests\DuskTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Dusk\Browser;
use Throwable;

class UserEditViewTest extends DuskTestCase
{
    use WithFaker;

    protected $user;

    /**
     * @throws Throwable
     */
    public function setUp(): void
    {
        parent::setUp();

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
            'status' => UserStatus::ACTIVE->value,
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
            'status' => UserStatus::SUSPENDED->value,
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
            'status' => UserStatus::ACTIVE->value,
        ]);

        $this->browse(function (Browser $browser) use ($super_admin) {
            $browser->visit(route('core.admin.user.view', ['user' => $super_admin->_id]))
                ->press('Block')
                ->waitForText('Are you sure you want to block this user?')
                ->clickAtXPath('//*[@id="banModal"]/div/div/form/div[3]/button[2]')
                ->assertSee('User Blocked successfully.');
        });
    }

    /**
     * @test
     * @throws Throwable
     */
    public function EditUserTest()
    {
        $file = UploadedFile::fake()->create('random.jpg')->store('public/dusk/avatars');

        $this->browse(function (Browser $browser) use ($file) {
            $browser->visit(route('core.admin.user.view', ['user' => $this->user]))
                ->attach('avatar', storage_path('app/'.$file))
                ->type('username', $this->faker()->userName)
                ->type('email', $this->faker()->email)
                ->type('bio', $this->faker()->text)
                ->clickAtXPath('//*[@id="user-info"]/form/div[4]/label') //email verified
                ->clickAtXPath('//*[@id="user-info"]/form/div[6]/label') //feature tips
                ->clickAtXPath('//*[@id="user-info"]/form/div[7]/label') //feature demo user
                ->select('gender')
                ->check('interested_in')
                ->select('type')
                ->select('role')
                ->select('status')
                ->select('visibility')
                ->clickAtXPath('//*[@id="user-info"]/form/div[18]/div/button')
                ->assertSee('User updated successfully.');
        });
    }

    /**
     * @test
     * @throws Throwable
     */
    public function UserMediaListTest()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('core.admin.user.view', ['user' => $this->user]))
               ->clickLink('Medias')
               ->assertSee('Cover')
               ->assertSee('Created By')
               ->assertSee('Description')
               ->assertSee('Status')
               ->assertSee('Likes')
               ->assertSee('Sort Score')
               ->assertSee('Created At');
        });
    }

    /**
     * @test
     * @throws Throwable
     */
    public function UserReuploadMediaTest()
    {
        $file = UploadedFile::fake()->create('random.mp4')->store('public/dusk/medias');

        $this->browse(function (Browser $browser) use ($file) {
            $browser->visit(route('core.admin.user.view', ['user' => $this->user]))
                ->clickLink('Upload')
                ->attach('.flow-browse input', storage_path('app/'.$file))
                ->assertSee('Uploading')
                ->assertSee('completed')
                ->type('description', $this->faker()->text)
                ->click('.upload-video-button')
                ->assertSee('New media saved');
        });
    }

    /**
     * @test
     * @throws Throwable
     */
    public function UserPaymentsTest()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('core.admin.user.view', ['user' => $this->user]))
                ->clickLink('Payments')
                ->assertSee('Credit Cards')
                ->assertSee('Earned Tips')
                ->assertSee('Send Tips')
                ->assertSee('Subscriptions')
                ->assertSee('Subscribers');
        });
    }
}
