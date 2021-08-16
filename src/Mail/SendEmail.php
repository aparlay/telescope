<?php

namespace Aparlay\Core\Mail;

use Aparlay\Core\Models\Email;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendEmail extends Mailable
{
    use Queueable;
    use SerializesModels;

    protected string $emailSubject;
    protected string $type;
    protected array $payload;

    /**
     * SendEmail Construct
     *
     * @param string $emailSubject
     * @param string $type
     * @param array $payload
     * @return void
     */
    public function __construct(string $emailSubject, string $type, array $payload)
    {
        $this->emailSubject = $emailSubject;
        $this->type = $type;
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
        switch ($this->type) {
            case Email::TEMPLATE_EMAIL_VERIFICATION:
                $template = 'default_view::email_verification';
                $verificationTemplate = config('app.email.templates.email_verification', 'default_view::email_verification');
                if (view()->exists($verificationTemplate)) {
                    $template = 'email_verification';
                }
                break;
            default:
                $template = '';
                break;
        }

        return $template;
    }
}
