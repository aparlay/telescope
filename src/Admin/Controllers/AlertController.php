<?php

namespace Aparlay\Core\Admin\Controllers;

use Aparlay\Core\Admin\Models\Alert;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    protected $alertService;

    public function __construct() {
        AlertService $alertService
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
            $media = $this->findMediaModel($model->media_id);
            $user = $this->findUserModel($model->user_id);

            $model->media_id = $media->_id ?? null;
            $model->user_id = $user->_id;

            $viewUrl = $model->media_id ? ['/media/view', 'id' => (string)$model->media_id] : ['/user/view', 'id' => (string)$model->user_id];

            if ($model->save()) {
                Yii::$app->session->setFlash('success', Yii::t('app', 'Alert saved successfully.'));
            } else {
                $text = array_values(ArrayHelper::getColumn($model->errors, '0'));
                if ($text) {
                    Yii::$app->session->setFlash('danger', implode("<br/>", $text));
                }
            }

            if (($post = Yii::$app->request->get('post-action', false)) !== false) {
                return $this->redirect($post);
            }

            return $this->redirect($viewUrl);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }
}
