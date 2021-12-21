<?php

namespace Aparlay\Core\Api\V1\Repositories;

use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Api\V1\Models\Report;
use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Api\V1\Requests\ReportRequest;
use Aparlay\Core\Models\Enums\ReportStatus;
use Aparlay\Core\Models\Enums\ReportType;
use Illuminate\Support\Facades\Log;
use MongoDB\BSON\ObjectId;

class ReportRepository
{
    protected Report $model;

    public function __construct($model)
    {
        if (! ($model instanceof Report)) {
            throw new \InvalidArgumentException('$model should be of Report type');
        }

        $this->model = $model;
    }

    /**
     * Responsible to create report for given user.
     *
     * @param User $user
     * @param ReportRequest $request
     * @return \Illuminate\Database\Eloquent\Model|Report|null
     */
    public function createUserReport(User $user, ReportRequest $request)
    {
        try {
            return Report::create([
                'reason' => $request->post('reason'),
                'type' => ReportType::USER->value,
                'status' => ReportStatus::REPORTED->value,
                'user_id' => new ObjectId($user->_id),
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return null;
        }
    }

    /**
     * Responsible to create report for given media.
     *
     * @param Media $media
     * @param ReportRequest $request
     * @return \Illuminate\Database\Eloquent\Model|Report|null
     */
    public function createMediaReport(Media $media, ReportRequest $request)
    {
        try {
            return Report::create([
                'reason' => $request->post('reason'),
                'type' => ReportType::MEDIA->value,
                'status' => ReportStatus::REPORTED->value,
                'media_id' => new ObjectId($media->_id),
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return null;
        }
    }
}
