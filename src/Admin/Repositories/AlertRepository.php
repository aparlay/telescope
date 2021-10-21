<?php

namespace Aparlay\Core\Admin\Repositories;

use Aparlay\Core\Admin\Models\Alert;
use Aparlay\Core\Admin\Models\Media;
use Aparlay\Core\Admin\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AlertRepository implements RepositoryInterface
{
    protected Alert $model;

    public function __construct($model)
    {
        if (! ($model instanceof Alert)) {
            throw new \InvalidArgumentException('$model should be of Alert type');
        }

        $this->model = $model;
    }

    public function all()
    {
    }

    /**
     * Create alert.
     *
     * @param array $data
     * @return Alert|null
     */
    public function store(Request $request)
    {
        try {
            return Alert::create($request->all());
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return null;
        }
    }

    public function create(array $data)
    {
        // TODO: Implement create() method.
    }

    public function update(array $data, $id)
    {
    }

    public function delete($id)
    {
        // TODO: Implement delete() method.
    }

    public function find($id)
    {
        return $this->model->findOrFail($id);
    }
}
