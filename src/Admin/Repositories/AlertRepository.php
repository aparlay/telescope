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
        return $this->model->orderBy('created_at', 'desc')->paginate(20);
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

    /**
     * Finds the Alert model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $_id
     * @return User the loaded model
     * @throws ValidationException if the model cannot be found
     */
    public function findUserModel($user_id)
    {
        if (($model = User::find($user_id)) !== null) {
            return $model;
        }

        throw ValidationException::withMessages([
            'app' => ['The requested user does not exist.'],
        ]);
    }

    /**
     * Finds the Alert model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $_id
     * @return Media the loaded model
     */
    public function findMediaModel($media_id)
    {
        $model = Media::find($media_id);
        if (($model = Media::find($media_id)) !== null) {
            return $model;
        }

        return null;
    }
}
