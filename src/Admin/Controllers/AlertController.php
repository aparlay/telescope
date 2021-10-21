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
        $msg = [];
        if ($return = $this->alertService->create($request)) {
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

        return redirect('/media/'.$request->media_id)->with($msg['type'], $msg['text']);
    }
}
