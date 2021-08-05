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
        View::addNamespace('template', base_path() . '/packages/Aparlay/Core/resources/views');
        switch ($type) {
            case 'email_verification':
                return 'template::email_verification_template';
                break;
            
            default:
                return '';
                break;
        }
    }
}
