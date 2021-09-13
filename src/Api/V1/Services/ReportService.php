<?php

namespace Aparlay\Core\Api\V1\Services;

use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Api\V1\Models\Report;
use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Api\V1\Repositories\ReportRepository;
use Aparlay\Core\Api\V1\Requests\ReportRequest;

class ReportService
{
    protected ReportRepository $reportRepository;

    public function __construct()
    {
        $this->reportRepository = new ReportRepository(new Report());
    }

    /**
     * Responsible to create report for given user.
     *
     * @param User $user
     * @param ReportRequest $request
     * @return array
     */
    public function createUserReport(User $user, ReportRequest $request)
    {
        return $this->reportRepository->createUserReport($user, $request);
    }

    /**
     * Responsible to create report for given media.
     *
     * @param Media $media
     * @param ReportRequest $request
     * @return array
     */
    public function createMediaReport(Media $media, ReportRequest $request)
    {
        return $this->reportRepository->createMediaReport($media, $request);
    }
}
