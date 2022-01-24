<?php

namespace Aparlay\Core\Api\V1\Repositories;

use Aparlay\Core\Api\V1\Models\Alert;
use MongoDB\BSON\ObjectId;

class AlertRepository
{
    protected Alert $model;

    public function __construct($model)
    {
        if (! ($model instanceof Alert)) {
            throw new \InvalidArgumentException('$model should be of Alert type');
        }

        $this->model = $model;
    }

    /**
     * Check if already Alerted by the given user.
     *
     * @param  array  $data
     * @param ObjectId $alertId
     * @return Alert
     */
    public function update(array $data, ObjectId|string $alertId): Alert
    {
        $alertId = $alertId instanceof ObjectId ? $alertId : new ObjectId($alertId);
        $alert = $this->model::find($alertId);
        $alert->fill($data);

        return $alert;
    }
}
