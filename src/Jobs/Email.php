<?php

namespace Aparlay\Core\Jobs;

use Aparlay\Core\Mail\EmailEnvelope;
use Aparlay\Core\Models\Enums\EmailStatus;
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
     * @param  string  $emailId
     * @param  string  $emailAddress
     * @param  string  $subject
     * @param  string  $type
     * @param  array   $payload
     */
    public function __construct(
        protected string $emailId,
        protected string $emailAddress,
        protected string $subject,
        protected string $type,
        protected array $payload
    ) {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $send = new EmailEnvelope($this->emailId, $this->subject, $this->type, $this->payload);
        Mail::to($this->emailAddress)->send($send);
        \Aparlay\Core\Models\Email::query()->email($this->emailId)->update(['status' => EmailStatus::SENT->value]);
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
