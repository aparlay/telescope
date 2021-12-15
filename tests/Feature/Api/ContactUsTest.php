<?php

namespace Aparlay\Core\Tests\Feature\Api;

class ContactUsTest extends ApiTestCase
{
    /**
     * @test
     */
    public function sendContactUs()
    {
        $this->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->json('POST', '/v1/contact-us/', [
                'email' => 'test@gmail.com',
                'name' => 'Dummy',
                'topic' => 'Account Problem',
                'message' => 'Having a problem with account'
            ])
            ->assertStatus(200)
            ->assertJsonPath('status', 'OK')
            ->assertJsonPath('code', 200);
    }
}
