<?php

namespace Aparlay\Core\Admin\Observers;

use Aparlay\Core\Admin\Models\Media;
use Aparlay\Core\Admin\Models\Media as ModelsMedia;
use Aparlay\Core\Admin\Models\User;
use Aparlay\Core\Admin\Models\User as ModelsUser;
use Aparlay\Core\Admin\Services\MediaService;
use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Jobs\DeleteMediaLike;
use Aparlay\Core\Jobs\UploadMedia;
use Exception;
use MongoDB\BSON\ObjectId;

class MediaObserver
{
    /**
     * Create a new event instance.
     *
     * @param Media $media
     * @return void
     * @throws Exception
     */
    public function created($media)
    {
        die('====created=====');
        // if ($media || isset($changedAttributes['status'])) {

        //     foreach ($this->user->medias as $media) {
        //         if ((string)$media['_id'] === (string)$this->_id) {
        //             $this->user->removeFromSet('medias', $media);
        //         }
        //     }

        //     $this->user->media_count = ModelsMedia::find()->creator($this->created_by)->count();
        //     $medias = [];
        //     foreach (ModelsMedia::find()->creator($this->created_by)->completed()->recentFirst()->limit(30)->asArray()->all() as $media) {
        //         $basename = basename($media['file'], '.' . pathinfo($media['file'], PATHINFO_EXTENSION));
        //         $file = Yii::$app->params['cdn']['videos'] . $basename . '.' . pathinfo($media['file'], PATHINFO_EXTENSION);
        //         $cover = Yii::$app->params['cdn']['covers'] . $basename . '.jpg';
        //         $medias[] = ['_id' => $media['_id'], 'file' => $file, 'cover' => $cover, 'status' => $media['status']];
        //     }
        //     $this->user->medias = $medias;
        //     $this->user->count_fields_updated_at = ArrayHelper::merge(
        //         $this->user->count_fields_updated_at,
        //         ['medias' => DT::utcNow()]
        //     );
        //     $this->user->save();
        // }

        // if ($this->status === ModelsMedia::STATUS_COMPLETED || $this->status === ModelsMedia::STATUS_CONFIRMED) {
        //     Yii::$app->cache->delete('Media.Index.TotalCount.Public');
        // }

        // if ($this->status === ModelsMedia::STATUS_ADMIN_DELETED) {
        //     Yii::$app->cache->delete('Media.Index.TotalCount.Public');

        //     $this->user->setScenario(ModelsUser::SCENARIO_UPDATE_COUNTERS);
        //     $this->user->media_count--;
        //     $file = Yii::$app->params['cdn']['videos'] . $this->file;
        //     $cover = Yii::$app->params['cdn']['covers'] . $this->filename . '.jpg';
        //     $this->user->removeFromSet('medias', ['_id' => $this->_id, 'file' => $file, 'cover' => $cover, 'status' => $this->status]);
        //     $this->user->count_fields_updated_at = ArrayHelper::merge(
        //         $this->user->count_fields_updated_at,
        //         ['medias' => DT::utcNow()]
        //     );
        //     $this->user->save();

        //     Yii::$app->queue->priority(10)->push(new DeleteMediaLikeJob(['media_id' => $this->_id]));
        // }

        // if ($this->reupload_file) {
        //     Yii::$app->queue->priority(5)->push(new UploadVideoJob(['file' => $this->file, 'media_id' => (string)$this->_id]));
        //     Yii::info ( 'upload media job created: ' . $this->file, __METHOD__ );
        // }

        // parent::afterSave($insert, $changedAttributes);
    }

    /**
     * Create a new event instance.
     *
     * @param Media $model
     * @return void
     * @throws Exception
     */
    public function saving($model): void
    {
        die('====saving=====');
        // $description = trim($this->description);
        // $tags = $people = [];
        // foreach (explode(' ', $description) as $item) {
        //     if (isset($item[0]) && $item[0] === '#' && substr_count($item, '#') === 1) {
        //         $tags[] = substr($item, 1);
        //     }
        //     if (isset($item[0]) && $item[0] === '@' && substr_count($item, '@') === 1) {
        //         $people[] = substr($item, 1);
        //     }
        // }
        // $this->hashtags = array_slice($tags, 0, 20);
        // $people = array_slice($people, 0, 20);
        // $users = [];
        // foreach (ModelsUser::find()->select(['username' => 1, 'avatar' => 1, '_id' => 1])->andWhere(['username' => $people])->limit(20)->asArray()->all() as $user) {
        //     $users[] = $user;
        // }
        // $this->people = $users;
        // if (!$this->hasErrors()) {
        //     $creator = ModelsUser::findOne($this->creator['_id']);
        //     $this->creator = ['_id' => $creator->_id, 'username' => $creator->username, 'avatar' => $creator->avatar];
        // }

        // if ($this->skin_score || $this->awesomeness_score){
        //     $this->scores = [
        //         [
        //             'type' => 'skin',
        //             'score' => (int)$this->skin_score
        //         ],
        //         [
        //             'type' => 'awesomeness',
        //             'score' => (int)$this->awesomeness_score
        //         ]
        //     ];
        //     $this->sort_score = $this->awesomeness_score + ($this->time_score / 2) + ($this->like_score / 3) + ($this->visit_score / 5);
        // }

        // if (!$model && $this->reupload_file) {
        //     if (($file = UploadedFile::getInstanceByName('file')) !== null || ($file = UploadedFile::getInstance($this, 'file')) !== null) {
        //         $this->file = uniqid('tmp_', true) . '.' . $file->extension;
        //         $path = Yii::getAlias('@uploadDir') . '/' . $this->file;
        //         if ($file->saveAs($path, false)) {
        //             Yii::info('New media saved: ' . $this->file, __METHOD__);
        //         } else {
        //             $this->addError('file', \Yii::t('app', 'Cannot upload the file.'));

        //             return false;
        //         }
        //     } elseif (!empty($this->file)) {
        //         if (!file_exists(Yii::getAlias('@uploadDir') . '/' . $this->file)) {
        //             $this->addError('file', \Yii::t('app', 'Uploaded file does not exists.'));

        //             return false;
        //         }
        //     }
        // }

        // return parent::beforeSave($model);
    }

    /**
     * Create a new event instance.
     *
     * @param Media $model
     * @return void
     * @throws Exception
     */
    public function creating($model): void
    {
        die('====creating=====');
    }

    /**
     * Create a new event instance.
     *
     * @param Media $media
     * @return void
     * @throws Exception
     */
    public function saved($media): void
    {
        die('====saved=====');
    }

    /**
     * Create a new event instance.
     *
     * @param Media $media
     * @return void
     * @throws Exception
     */
    public function deleted($media): void
    {
        die('====deleted=====');
    }
}
