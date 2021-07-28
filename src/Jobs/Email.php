<?php

namespace Aparlay\Core\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Aparlay\Core\Mail\SendEmail;
use Swift_SmtpTransport;
use Swift_Message;

class Email implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $send_mail;
    protected $send_from = 'john@doe.com';
    protected $sendFromName = 'John Doe';
    protected $SendEmailBody;
    protected $EmailSubject;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($EmailSubject, $send_mail, $SendEmailBody)
    {
        $this->send_mail = $send_mail;
        $this->SendEmailBody = $SendEmailBody;
        $this->EmailSubject = $EmailSubject;
        $this->handle();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {  
        // Mail::to($this->send_mail)->send($email);
        // Create a message
        $message = (new Swift_Message($this->EmailSubject))
        ->setFrom([$this->send_from => $this->sendFromName])
        ->setTo([$this->send_mail])
        ->setBody($this->SendEmailBody);


        $transport = (new Swift_SmtpTransport(config('mail.mailers.Swift_SmtpTransport.host'), config('mail.mailers.Swift_SmtpTransport.port')))
                            ->setUsername(config('mail.mailers.Swift_SmtpTransport.username'))
                            ->setEncryption(config('mail.mailers.Swift_SmtpTransport.encryption'))
                            ->setPassword(config('mail.mailers.Swift_SmtpTransport.password'));
                
        $email = new SendEmail($transport, $message); 
    }
}
