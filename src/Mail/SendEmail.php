<?php

namespace Aparlay\Core\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Swift_Mailer;

class SendEmail extends Mailable
{
    use Queueable, SerializesModels;
    protected $transport;
    protected $message;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($transport, $message)
    {
        $this->transport = $transport;
        $this->message = $message;
        $this->build();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // Create the Mailer using your created Transport
        $mailer = new Swift_Mailer($this->transport);
        
        // Send the message
        $result = $mailer->send($this->message);
    }
}
