<?php

namespace Aparlay\Core\Api\V1\Controllers;

use Aparlay\Core\Jobs\Email;
use Aparlay\Core\Api\V1\Requests\ContactUsRequest;
use Aparlay\Core\Notifications\ContactUs;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Aparlay\Core\Models\User;

class ContactUsController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function send(ContactUsRequest $request): Response
    {   $user =
        $data = $request->input();
        $data['msg'] =  $data['message'];

        $user = User::admin()->first();
        $user->notify(
            new ContactUs(config('mail.support_email'),$data['name'],'Contact Us notification',$data['message'])
        );
        dispatch((new Email(config('mail.support_email'), 'Contact Us notification', 'email_contactus',$data))->onQueue('medium'));
        return $this->response([], Response::HTTP_OK);
    }
}
