<?php

namespace Aparlay\Core\Api\V1\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Spatie\Health\Commands\RunHealthChecksCommand;
use Spatie\Health\ResultStores\ResultStore;
use Spatie\Health\ResultStores\StoredCheckResults\StoredCheckResult;

class HealthCheckJsonResultsController extends Controller
{
    protected const IGNORED_CHECKS = [
        'UsedDiskSpace',
        'Environment',
        'DebugMode',
        'CpuLoad',
        'MeilisearchSsl',
        'ApiSsl',
        'SocketSsl',
    ];

    public function __invoke(Request $request, ResultStore $resultStore): Response
    {
        if ($request->has('fresh') || config('health.oh_dear_endpoint.always_send_fresh_results')) {
            Artisan::call(RunHealthChecksCommand::class);
        }

        $checkResults = $resultStore->latestResults();

        $response     = [
            'finishedAt' => $checkResults->finishedAt->getTimestamp(),
            'checkResults' => $checkResults->storedCheckResults->map(fn (StoredCheckResult $line) => $line->toArray()),
        ];

        $result       = $checkResults->storedCheckResults
            ->map(fn (StoredCheckResult $line) => $line->toArray())
            ->filter(
                fn (array $check) => $check['status'] === 'ok' || in_array($check['name'], self::IGNORED_CHECKS)
            )
            ->count() !== 0;

        return $result ?
            $this->error($response, [], Response::HTTP_SERVICE_UNAVAILABLE) :
            $this->response($response, '', Response::HTTP_OK);
    }
}
