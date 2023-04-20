<?php

namespace Aparlay\Core\Jobs;

abstract class AbstractJob
{
    /**
     * The number of times the job may be attempted.
     */
    public int $tries         = 1;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     */
    public int $maxExceptions = 1;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int|array
     */
    public $backoff           = 1;

    public function __construct()
    {
        $this->tries         = config('queue.settings.tries');
        $this->maxExceptions = config('queue.settings.max_exceptions');
        $this->backoff       = config('queue.settings.backoff');
    }
}
