<?php

namespace Aparlay\Core\Admin\Controllers;

use Aparlay\Core\Admin\Services\MediaService;
use Aparlay\Core\Jobs\ReprocessMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    protected $mediaService;

    public function __construct(
        MediaService $mediaService
    ) {
        $this->mediaService = $mediaService;
    }

    /**
     * @throws \ErrorException
     */
    public function index()
    {
        $breadcrumbs = [
            'title' => 'Media',
        ];

        $medias = $this->mediaService->getList();

        return view('default_view::admin.pages.media.index')->with([
            'media_list'  => $medias,
            'breadcrumbs' => $breadcrumbs,
        ]);
    }

    public function view($id)
    {
        $media = $this->mediaService->find($id);
        $skinScore = $this->mediaService->skinScore();
        $awesomenessScore = $this->mediaService->awesomenessScore();
        $scoreTypes = $media->scores;

        return view('default_view::admin.pages.media.view', compact('media', 'skinScore', 'awesomenessScore', 'scoreTypes'));
    }

    public function update(Request $request, $id)
    {
        $this->mediaService->updateMedia($request, $id);
        $media = $this->mediaService->find($id);
        $skinScore = $this->mediaService->skinScore();
        $awesomenessScore = $this->mediaService->awesomenessScore();
        $scoreTypes = $media->scores;

        return view('default_view::admin.pages.media.view', compact('media', 'skinScore', 'awesomenessScore', 'scoreTypes'));
    }

    public function reprocess($id)
    {
        $media = $this->mediaService->find($id);

        ReprocessMedia::dispatch($media->_id, $media->file)->onQueue('lowpriority');

        return redirect('media/'.(string) $media->_id)->with('success', 'Video is placed in queue for reprocessing.');
    }

    public function downloadOriginal($id, $hash = '')
    {
        $media = $this->mediaService->find($id);
        $matchedFile = $media->files_history[0] ?? [
            'hash' => $media->hash,
            'size' => $media->size,
            'mime_type' => $media->mime_type,
            'file' => $media->file,
        ];
        foreach ($media->files_history as $file) {
            if ($file['hash'] === $hash) {
                $matchedFile = $file;
                break;
            }
        }

        $backblazeBucket = config('app.backblaze.videos.bucket');
        $backblaze = Storage::create(Storage::STORAGE_BACKBLAZE, $backblazeBucket);

        try {
            $b2File = $matchedFile['file'];
            if ($backblaze->fileExists($b2File)) {
                return Yii::$app->response->sendStreamAsFile(
                    $backblaze->readStream($b2File),
                    'orig.'.$b2File,
                    [
                        'fileSize' => $backblaze->fileSize($b2File),
                        'mimeType' => $backblaze->mimeType($b2File),
                        'inline' => false,
                    ]
                );
            }
            Yii::$app->session->setFlash('error', Yii::t('app', 'Video file not found.'));
        } catch (\Exception $e) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'Video file download failed.'));
        }

        return $this->redirect(['media/view', 'id' => $id]);
    }
}
