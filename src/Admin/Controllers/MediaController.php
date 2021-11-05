<?php

namespace Aparlay\Core\Admin\Controllers;

use Aparlay\Core\Admin\Models\Media;
use Aparlay\Core\Admin\Requests\MediaUploadRequest;
use Aparlay\Core\Admin\Requests\MediaUpdateRequest;
use Aparlay\Core\Admin\Resources\MediaResource;
use Aparlay\Core\Admin\Services\MediaService;
use Aparlay\Core\Jobs\ReprocessMedia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;
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
        $mediaStatuses = $this->mediaService->getMediaStatuses();

        return view('default_view::admin.pages.media.index')->with([
            'moderation' => false,
            'mediaStatuses' => $mediaStatuses,
        ]);
    }

    public function moderation()
    {
        $mediaStatuses = $this->mediaService->getMediaStatuses();

        return view('default_view::admin.pages.media.index')->with([
            'moderation' => true,
            'mediaStatuses' => $mediaStatuses,
        ]);
    }

    public function indexAjax(Route $route)
    {
        return new MediaResource($this->mediaService->getFilteredMedia());
    }

    public function view(Media $media)
    {
        $media = new MediaResource($this->mediaService->find($media->_id));
        $scoreTypes = $media->scores ?? [['type' => 'skin', 'score' => 0], ['type' => 'awesomeness', 'score' => 0]];

        return view('default_view::admin.pages.media.view', compact('media', 'scoreTypes'));
    }

    /**
     * @param MediaUpdateRequest $request
     * @param $id
     * @return RedirectResponse
     */
    public function update(Media $media, MediaUpdateRequest $request): RedirectResponse
    {
        $this->mediaService->update($media->_id);

        return redirect()->back();
    }

    public function reprocess(Media $media)
    {
        $media = $this->mediaService->find($media->_id);

        ReprocessMedia::dispatch($media->_id, $media->file)->onQueue('lowpriority');

        return redirect()->route('core.admin.media.view', ['media' => (string) $media->_id])->with('success', 'Video is placed in queue for reprocessing.');
    }

    public function pending(Media $media, $order)
    {
        $order = (int) $order;
        $models = [];

        if (in_array($order, [SORT_ASC, SORT_DESC], true)) {
            $models = $this->mediaService->pending($order);
        }

        foreach ($models as $model) {
            if ($media->_id != (string) $model->_id) {
                return redirect()->route('core.admin.media.view', ['media' => (string) $model->_id]);
            }
        }

        return redirect()->route('core.admin.media.index');
    }

    public function downloadOriginal(Media $media, $hash = '')
    {
        $media = $this->mediaService->find($media->_id);
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

        $backblaze = Storage::disk('b2-videos');

        try {
            $b2File = $matchedFile['file'];
            if ($backblaze->exists($b2File)) {
                return $backblaze->download($b2File, 'orig.'.$b2File, ['Content-Type' => $backblaze->mimeType($b2File)]);
            }

            return redirect()->route('core.admin.media.view', ['media' => $media->_id])->with('danger', 'Video file not found.');
        } catch (\Exception $e) {
            return redirect()->route('core.admin.media.view', ['media' => $media->_id])->with('danger', 'Video file download failed.');
        }

        return redirect()->route('core.admin.media.view', ['media' => $media->_id]);
    }

    public function reupload(Media $media, MediaUploadRequest $request)
    {
        $this->mediaService->reupload($media);

        return redirect()->back()->with(['success' => 'Video uploaded successfully']);
    }
}
