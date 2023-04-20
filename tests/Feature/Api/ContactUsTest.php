<?php

namespace Aparlay\Core\Tests\Feature\Api;

use Aparlay\Core\Models\User;
use Aparlay\Core\Notifications\ContactUs;
use Biscolab\ReCaptcha\Facades\ReCaptcha;
use Illuminate\Support\Facades\Notification;

class ContactUsTest extends ApiTestCase
{
    /**
     * @test
     */
    public function send_contact_us()
    {
        Notification::fake();
        // TODO: it seems mocking doesnt work correctly
        // ReCaptcha::shouldReceive('validate')->once()->andReturnTrue();

        $this->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->json('POST', '/v1/contact-us/', [
                'email' => 'test@gmail.com',
                'name' => 'Dummy',
                'topic' => 'Account Problem',
                'message' => 'Having a problem with account',
            ])
            ->assertStatus(200)
            ->assertJsonPath('status', 'OK')
            ->assertJsonPath('code', 200);

        $user = User::admin()->first();
        Notification::assertSentTo(
            [$user],
            ContactUs::class
        );
    }
}
