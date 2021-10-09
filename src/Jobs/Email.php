<?php

namespace Aparlay\Core\Jobs;

use Aparlay\Core\Mail\EmailEnvelope;
use Aparlay\Core\Models\User;
use Aparlay\Core\Notifications\JobFailed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Mail;
use Throwable;

class Email implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected string $email;
    protected string $subject;
    protected string $type;
    protected array $payload;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 30;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     */
    public int $maxExceptions = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int|array
     */
    public $backoff = 10;

    /**
     * Create a new job instance.
     *
     * @param string $email
     * @param string $subject
     * @param string $type
     * @param array $payload
     * @return void
     */
    public function __construct(string $email, string $subject, string $type, array $payload)
    {
        $this->email = $email;
        $this->subject = $subject;
        $this->type = $type;
        $this->payload = $payload;
    }

    /**
     * Execute the job.
     * @return void
     */
    public function handle()
    {
        $send = new EmailEnvelope($this->subject, $this->type, $this->payload);
        Mail::to($this->email)->send($send);
    }

    /**
     * @param  Throwable  $exception
     */
    public function failed(Throwable $exception): void
    {
        if (($user = User::admin()->first()) !== null) {
            $user->notify(new JobFailed(self::class, $this->attempts(), $exception->getMessage()));
        }
    }
}
