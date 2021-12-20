<?php

namespace Aparlay\Core\Api\V1\Repositories;

use Aparlay\Core\Api\V1\Dto\ReportDTO;
use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Api\V1\Models\Report;
use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Api\V1\Requests\ReportRequest;
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
    public function createUserReport(User $user, ReportDTO $reportDTO)
    {
        try {
            return Report::create([
                'reason' => $reportDTO->reason,
                'type' => Report::TYPE_USER,
                'status' => Report::STATUS_REPORTED,
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
    public function createMediaReport(Media $media, ReportDTO $reportDTO)
    {
        try {
            return Report::create([
                'reason' => $reportDTO->reason,
                'type' => Report::TYPE_MEDIA,
                'status' => Report::STATUS_REPORTED,
                'media_id' => new ObjectId($media->_id),
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return null;
        }
    }
}
