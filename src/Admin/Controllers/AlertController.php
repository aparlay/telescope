<?php

namespace Aparlay\Core\Admin\Controllers;

use Aparlay\Core\Admin\Models\Alert;
use Aparlay\Core\Admin\Services\AlertService;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    protected $alertService;

    public function __construct(AlertService $alertService)
    {
        $this->alertService = $alertService;
    }

    /**
     * @throws \ErrorException
     */
    public function index()
    {
    }

    public function view($id)
    {
    }

    /**
     * Creates a new Alert model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function create(Request $request)
    {
        $model = new Alert();

        if ($request->all()) {
            $media_id = $request->media_id;
            $user_id = $request->user_id;

            $media = $this->alertService->findMediaModel($media_id);
            $user = $this->alertService->findUserModel($user_id);
            $media_id = $media->_id ?? null;
            $user_id = $user->_id;

            $viewUrl = $media_id ? ['/media/', 'id' => (string) $media_id] : ['/user/view', 'id' => (string) $user_id];

            $msg = [];
            if ($this->alertService->create($request)) {
                $msg = [
                    'type' => 'success',
                    'text' => 'Alert saved successfully.',
                ];
            } else {
                $msg = [
                    'type' => 'danger',
                    'text' => 'There are some issues.',
                ];
            }

            if (($post = $request->get('post-action', false)) !== false) {
                return redirect($post)->with($msg['type'], $msg['text']);
            }

            return redirect($viewUrl['0'].$viewUrl['id'])->with($msg['type'], $msg['text']);
        }
    }
}
