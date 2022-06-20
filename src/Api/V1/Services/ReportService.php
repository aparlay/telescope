<?php

namespace Aparlay\Core\Api\V1\Services;

use Aparlay\Core\Api\V1\Dto\ReportDTO;
use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Api\V1\Models\MediaComment;
use Aparlay\Core\Api\V1\Models\Report;
use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Api\V1\Repositories\ReportRepository;
use Aparlay\Core\Api\V1\Requests\ReportRequest;
use Aparlay\Core\Models\Enums\ReportStatus;
use Aparlay\Core\Models\Enums\ReportType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use MongoDB\BSON\ObjectId;

class ReportService extends AbstractService
{
    /**
     * Responsible to create report for given user.
     *
     * @param User $user
     * @param ReportDTO $reportDTO
     * @return Model|Report|null
     */
    public function createUserReport(User $user, ReportDTO $reportDTO): Model|Report|null
    {
        try {
            return Report::create([
                'reason' => $reportDTO->reason,
                'type' => ReportType::USER->value,
                'status' => ReportStatus::REPORTED->value,
                'user_id' => new ObjectId($user->_id),
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return null;
        }
    }


     public function createCommentReport(MediaComment $comment, ReportDTO $reportDTO): Model|Report|null
    {
        try {
            return Report::create([
                'reason' => $reportDTO->reason,
                'type' => ReportType::COMMENT->value,
                'status' => ReportStatus::REPORTED->value,
                'comment_id' => new ObjectId($comment->_id),
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return null;
        }
    }



    /**
     * Responsible to create report for given media.
     *
     * @param  Media  $media
     * @param  ReportDTO  $reportDTO
     * @return Model|Report|null
     */
    public function createMediaReport(Media $media, ReportDTO $reportDTO): Model|Report|null
    {
        try {
            return Report::create([
                'reason' => $reportDTO->reason,
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
