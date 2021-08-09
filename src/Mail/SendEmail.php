<?php

namespace Aparlay\Core\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\View;

class SendEmail extends Mailable
{
    use Queueable;
    use SerializesModels;

    protected $content;

    public function __construct($content)
    {
        $this->content = $content;
        $this->build();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $template = $this->getTemplate($this->content['email_type']);
        return $this->subject($this->content['subject'])
            ->view($template)
            ->with($this->content['email_template_params']);
    }

    /**
     * Responsible to return the email template based on email type
     * @param String $type
     * @return String
     */
    public function getTemplate(string $type)
    {
        switch ($type) {
            case 'email_verification':
                if (config('app.email.template_urls.email_verification_template') && view()->exists(config('app.email.template_urls.email_verification_template'))) {
                    return 'email_verification_template';
                } else {
                    return 'default_view::email_verification_template';
                }
                break;
            
            default:
                return '';
                break;
        }
    }
}
