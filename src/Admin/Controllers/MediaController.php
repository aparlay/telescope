<?php

namespace Aparlay\Core\Admin\Controllers;

use Aparlay\Core\Admin\Resources\MediaResource;
use Aparlay\Core\Admin\Services\MediaService;
use ErrorException;
use Illuminate\Http\Request;

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
            'mediaStatuses' => $mediaStatuses
        ]);
    }

    public function indexAjax()
    {
        return new MediaResource($this->mediaService->getFilteredMedia());
    }

    public function view($id)
    {
        $media = $this->mediaService->find($id);
        $skin_score = $this->mediaService->skinScore();
        $awesomeness_score = $this->mediaService->awesomenessScore();
        $score_types = $media->scores;

        return view('default_view::admin.pages.media.view', compact('media', 'skin_score', 'awesomeness_score', 'score_types'));
    }

    public function update(Request $request, $id)
    {
        $this->mediaService->updateMedia($request, $id);
        $media = $this->mediaService->find($id);

        return view('default_view::admin.pages.media.view', compact('media'));
    }
}
