<?php

namespace Aparlay\Core\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Aparlay\Core\Mail\SendEmail;
use Mail;

class Email implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $send_mail;
    protected $subject;
    protected $params;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public int $tries = 10;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     *
     * @var int
     */
    public int $maxExceptions = 3;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($send_mail, $subject, $params)
    {
        $this->send_mail = $send_mail;
        $this->subject = $subject;
        $this->params = $params;
        $this->handle($send_mail);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle($send_mail)
    {
        $email = new SendEmail($this->subject, $this->params);
        Mail::to($this->send_mail)->send($email);
    }
}
