<?php

namespace Aparlay\Core\Api\V1\Controllers;

use Aparlay\Core\Api\V1\Repositories\EmailRepository;
use Aparlay\Core\Api\V1\Requests\ContactUsRequest;
use Aparlay\Core\Jobs\Email;
use Aparlay\Core\Models\Email as EmailModel;
use Aparlay\Core\Models\Enums\EmailStatus;
use Aparlay\Core\Models\Enums\EmailType;
use Aparlay\Core\Models\User;
use Aparlay\Core\Notifications\ContactUs;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use MongoDB\BSON\ObjectId;

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
        $request = [
            'to' => config('mail.support_email'),
            'status' => EmailStatus::QUEUED->value,
            'type' => EmailType::CONTACT->value,
        ];

        $email = EmailRepository::create($request);
        Email::dispatch((string)$email->_id, config('mail.support_email'), 'Contact Us notification', EmailModel::TEMPLATE_EMAIL_CONTACTUS, $data);

        $user = User::admin()->first();
        $user->notify(
            new ContactUs($data['email'], $data['name'], 'Contact Us notification', $data['message'])
        );

        return $this->response([], Response::HTTP_OK);
    }
}
