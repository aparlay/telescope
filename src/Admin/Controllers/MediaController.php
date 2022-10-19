<?php

namespace Aparlay\Core\Admin\Controllers;

use Aparlay\Core\Admin\Models\Media;
use Aparlay\Core\Admin\Requests\MediaUpdateRequest;
use Aparlay\Core\Admin\Requests\MediaUploadRequest;
use Aparlay\Core\Admin\Resources\MediaResource;
use Aparlay\Core\Admin\Services\MediaService;
use Aparlay\Core\Jobs\ReprocessMedia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Session;

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

    /**
     * @param $mediaId
     * @param $direction
     * @return RedirectResponse
     */
    public function moderationNextOrPrev($mediaId, $direction)
    {
        $currentUser = auth()->user();

        if ((int) $direction === 1) {
            $media = $this->mediaService->nextItemToReview($currentUser, $mediaId);
        } else {
            $media = $this->mediaService->prevItemToReview($currentUser, $mediaId);
        }

        if ($media) {
            return redirect()->route('core.admin.media.view', ['media' => $media->_id]);
        }

        return redirect()->route('core.admin.media.index')->with([
            'warning' => 'Moderation queue is empty',
        ]);
    }

    public function moderationQueue()
    {
        $currentUser = auth()->user();
        $media = $this->mediaService->firstItemToReview($currentUser);

        if ($media) {
            return redirect()->route('core.admin.media.view', ['media' => $media->_id])->with([]);
        }

        return redirect()->route('core.admin.media.index')->with([
            'warning' => 'Moderation queue is empty',
        ]);
    }

    public function moderation()
    {
        $mediaStatuses = $this->mediaService->getMediaStatuses();

        return view('default_view::admin.pages.media.moderation')->with([
            'moderation' => true,
            'mediaStatuses' => $mediaStatuses,
        ]);
    }

    public function view(Media $media)
    {
        $media = new MediaResource($this->mediaService->find($media->_id));
        $scoreTypes = ! empty($media->scores) ? $media->scores : [['type' => 'skin', 'score' => 0], ['type' => 'awesomeness', 'score' => 0]];

        $moderationQueueNotEmpty = $this->mediaService->isModerationQueueNotEmpty();

        $hasPrev = $this->mediaService->hasPrevItemToReview($media->_id);
        $hasNext = $this->mediaService->hasNextItemToReview($media->_id);

        $viewParams = [
            'media',
            'scoreTypes',
            'moderationQueueNotEmpty',
            'hasPrev',
            'hasNext',
        ];

        return view('default_view::admin.pages.media.view', compact($viewParams));
    }

    /**
     * @param MediaUpdateRequest $request
     * @param $id
     * @return RedirectResponse
     */
    public function update(Media $media, MediaUpdateRequest $request): RedirectResponse
    {
        $this->mediaService->update($media->_id);

        return redirect()->back()->with(['success' => 'Media updated successfully']);
    }

    public function reprocess(Media $media)
    {
        $media = $this->mediaService->find($media->_id);

        ReprocessMedia::dispatch($media->_id, $media->files_history[0]['file'])->onQueue('low');

        return redirect()->route('core.admin.media.view', ['media' => (string) $media->_id])->with('success', 'Video is placed in queue for reprocessing.');
    }

    public function pending($page = 1)
    {
        $models = $this->mediaService->pending($page);

        if ($models->currentPage() > $models->lastPage()) {
            return redirect()->route('core.admin.media.index');
        }

        $currentPage = $models->currentPage();
        $nextPage = $currentPage === $models->lastPage() ? 1 : $currentPage + 1;
        $prevPage = $currentPage === 1 ? $models->lastPage() : $currentPage - 1;

        foreach ($models as $model) {
            return redirect()->route('core.admin.media.view', ['media' => (string) $model->_id])->with(['prevPage' => $prevPage, 'nextPage' => $nextPage]);
        }
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
