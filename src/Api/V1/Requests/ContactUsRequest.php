<?php

namespace Aparlay\Core\Api\V1\Requests;

use Illuminate\Support\Facades\Request;

class ContactUsRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'topic' => ['required', 'string'],
            'name' => ['required', 'string'],
            'email' => ['required', 'string'],
            'message' => ['required', 'string'],
            'g-recaptcha-response' => function($attribute, $value, $fail) {
                $userIP =  Request::ip();
                $secretKey = config('recaptcha.api_secret_key');
                $recaptchaSiteVerifyURL = config('recaptcha.site_verify_url');
                $url = "$recaptchaSiteVerifyURL?secret=$secretKey&response=$value&remoteip=$userIP";
                $response = file_get_contents($url);
                $response = json_decode($response);

                if(!$response->success){
                    $fail('google reCaptcha failed.');
                }
            }
        ];
    }
}
