<?php

namespace Aparlay\Core\Api\V1\Repositories;

use Aparlay\Core\Api\V1\Dto\ReportDTO;
use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Api\V1\Models\Report;
use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Api\V1\Requests\ReportRequest;
use Aparlay\Core\Models\Enums\ReportStatus;
use Aparlay\Core\Models\Enums\ReportType;
use Exception;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use MongoDB\BSON\ObjectId;

class ReportRepository
{
    protected Report $model;

    public function __construct($model)
    {
        if (!($model instanceof Report)) {
            throw new InvalidArgumentException('$model should be of Report type');
        }

        $this->model = $model;
    }

    /**
     * Responsible to create report for given user.
     *
     * @param ReportRequest $request
     *
     * @return \Illuminate\Database\Eloquent\Model|Report|null
     */
    public function createUserReport(User $user, ReportDTO $reportDTO)
    {
        try {
            return Report::create([
                'reason' => $reportDTO->reason,
                'type' => ReportType::USER->value,
                'status' => ReportStatus::REPORTED->value,
                'user_id' => new ObjectId($user->_id),
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return;
        }
    }

    /**
     * Responsible to create report for given media.
     *
     * @param ReportRequest $request
     *
     * @return \Illuminate\Database\Eloquent\Model|Report|null
     */
    public function createMediaReport(Media $media, ReportDTO $reportDTO)
    {
        try {
            return Report::create([
                'reason' => $reportDTO->reason,
                'type' => ReportType::MEDIA->value,
                'status' => ReportStatus::REPORTED->value,
                'media_id' => new ObjectId($media->_id),
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return;
        }
    }
}
