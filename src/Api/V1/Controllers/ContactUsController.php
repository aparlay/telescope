<?php

namespace Aparlay\Core\Api\V1\Controllers;

use Aparlay\Core\Api\V1\Models\Alert;
use Aparlay\Core\Jobs\Email;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Aparlay\Core\Api\V1\Requests\ContactUsRequest;

class ContactUsController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function send( ContactUsRequest $request): Response
    {
        $data = $request->input();
        $data['msg'] =  $data['message'];
        dispatch((new Email(config('mail.support_email'), 'Contact Us notification', 'email_contactus',$data))->onQueue('medium'));
        return $this->response([], Response::HTTP_OK);
    }

}
