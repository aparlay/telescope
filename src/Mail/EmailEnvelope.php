<?php

namespace Aparlay\Core\Mail;

use Aparlay\Core\Models\Email;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailEnvelope extends Mailable
{
    use Queueable;
    use SerializesModels;

    protected string $emailSubject;
    protected string $template;
    protected array $payload;

    /**
     * SendEmail Construct.
     *
     * @param string $emailSubject
     * @param string $template
     * @param array $payload
     * @return void
     */
    public function __construct(string $emailSubject, string $template, array $payload)
    {
        $this->emailSubject = $emailSubject;
        $this->template = $template;
        $this->payload = $payload;
        $this->build();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->emailSubject)
            ->view($this->getTemplate())
            ->with($this->payload);
    }

    /**
     * Responsible to return the email template based on email type.
     * @return string
     */
    public function getTemplate()
    {
        switch ($this->template) {
            case Email::TEMPLATE_EMAIL_VERIFICATION:
                $template = 'default_view::email_verification';
                $verificationTemplate = config('app.email.templates.email_verification', 'default_view::email_verification');
                if (view()->exists($verificationTemplate)) {
                    $template = 'email_verification';
                }
                break;

            case Email::TEMPLATE_EMAIL_CONTACTUS:
                $template = 'default_view::email_contactus';
                $verificationTemplate = config('app.email.templates.email_contactus', 'default_view::email_contactus');
                if (view()->exists($verificationTemplate)) {
                    $template = 'default_view::email_contactus';
                }
                break;

            default:
                $template = '';
                break;
        }

        return $template;
    }
}
