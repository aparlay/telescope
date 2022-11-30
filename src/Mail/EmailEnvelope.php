<?php

namespace Aparlay\Core\Mail;

use Aparlay\Core\Models\Email;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;

class EmailEnvelope extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * SendEmail Construct.
     *
     * @param  string  $emailId
     * @param  string  $emailSubject
     * @param  string  $template
     * @param  array   $payload
     */
    public function __construct(
        protected string $emailId,
        protected string $emailSubject,
        protected string $template,
        protected array $payload
    ) {
        $this->build();
    }

    /**
     * Get the message headers.
     *
     * @return Headers
     */
    public function headers()
    {
        return new Headers(
            messageId: $this->emailId.'@'.config('app.main_domain'),
            references: [$this->emailId],
            text: [
                'X-Email-Id' => $this->emailId,
                'List-Unsubscribe' => '<https://www.waptap.com/unsubscribe/?email_id='.$this->emailId.'>, <mailto:unsubscribe@waptap.com?subject=unsubscribe.'.$this->emailId.'>',
            ],
        );
    }

    /**
     * Build the message.
     *
     * @return void
     */
    public function build(): void
    {
        $data = $this->payload;
        $data['unsubscribe_url'] = 'https://www.waptap.com/unsubscribe/?email_id='.$this->emailId;
        $this->subject($this->emailSubject)->view($this->getTemplate())->with($data);
    }

    /**
     * Responsible to return the email template based on email type.
     *
     * @return string
     */
    public function getTemplate()
    {
        switch ($this->template) {
            case Email::TEMPLATE_EMAIL_VERIFICATION:
                $template = 'default_view::email_verification';
                $verificationTemplate = config(
                    'app.email.templates.email_verification',
                    'default_view::email_verification'
                );
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
