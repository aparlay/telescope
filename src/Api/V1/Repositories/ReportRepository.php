<?php

namespace Aparlay\Core\Api\V1\Repositories;

use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Api\V1\Models\Report;
use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Api\V1\Notifications\ReportSent;
use Aparlay\Core\Api\V1\Requests\ReportRequest;
use MongoDB\BSON\ObjectId;

class ReportRepository implements RepositoryInterface
{
    protected Report $model;

    public function __construct($model)
    {
        if (! ($model instanceof Report)) {
            throw new \InvalidArgumentException('$model should be of Report type');
        }

        $this->model = $model;
    }

    public function all()
    {
        // TODO: Implement all() method.
    }

    /**
     * Create MediaLike.
     *
     * @param array $data
     */
    public function create(array $data)
    {
        // TODO: Implement create() method.
    }

    public function update(array $data, $id)
    {
        // TODO: Implement update() method.
    }

    public function delete($id)
    {
        // TODO: Implement delete() method.
    }

    public function find($id)
    {
        // TODO: Implement find() method.
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
        $this->model->reason = $request->post('reason');
        $this->model->type = Report::TYPE_USER;
        $this->model->status = Report::STATUS_REPORTED;
        $this->model->user_id = new ObjectId($user->_id);
        $this->model->save();
        $this->model->notify(new ReportSent());
        return $this->model;
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
        $this->model->reason = $request->post('reason');
        $this->model->type = Report::TYPE_MEDIA;
        $this->model->status = Report::STATUS_REPORTED;
        $this->model->media_id = new ObjectId($media->_id);
        $this->model->save();
        $this->model->notify(new ReportSent());
        return $this->model;
    }
}
