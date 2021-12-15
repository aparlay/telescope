<?php

namespace Aparlay\Core\Api\V1\Controllers;

use Aparlay\Core\Api\V1\Requests\ContactUsRequest;
use Aparlay\Core\Jobs\Email;
use Aparlay\Core\Models\Email as EmailModel;
use Aparlay\Core\Models\User;
use Aparlay\Core\Notifications\ContactUs;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ContactUsController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function send(ContactUsRequest $request): Response
    {
        $data = $request->input();
        $data['msg'] = $data['message'];

        dispatch((new Email(config('mail.support_email'), 'Contact Us notification', EmailModel::TEMPLATE_EMAIL_CONTACTUS, $data)));
        $user = User::admin()->first();
        $user->notify(
            new ContactUs(config('mail.support_email'), $data['name'], 'Contact Us notification', $data['message'])
        );

        return $this->response([], Response::HTTP_OK);
    }
}
