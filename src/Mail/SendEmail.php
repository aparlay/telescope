<?php

namespace Aparlay\Core\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\View;

class SendEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $subject;
    protected $params;

    public function __construct($subject, $params)
    {   
        $this->subject = $subject;
        $this->params = $params;
        $this->build();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {   
        View::addNamespace('newview', base_path().'/packages/Aparlay/Core/resources/views');

        return $this->subject($this->subject)
            ->view('newview::email')->with(['otp' => $this->params['otp'], 'tracking_url'=> $this->params['tracking_url']]);
    }
}
