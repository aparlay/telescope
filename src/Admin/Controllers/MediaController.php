<?php

namespace Aparlay\Core\Admin\Controllers;

use Aparlay\Core\Admin\Resources\MediaResource;
use Aparlay\Core\Admin\Services\MediaService;
use ErrorException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

    public function view($id)
    {
        $media = new MediaResource($this->mediaService->find($id));
        $skinScore = $this->mediaService->skinScore();
        $awesomenessScore = $this->mediaService->awesomenessScore();
        $scoreTypes = $media->scores;

        return view('default_view::admin.pages.media.view', compact('media', 'skinScore', 'awesomenessScore', 'scoreTypes'));
    }

    public function update(Request $request, $id)
    {
        $this->mediaService->update($id);
        $media = $this->mediaService->find($id);
        $skinScore = $this->mediaService->skinScore();
        $awesomenessScore = $this->mediaService->awesomenessScore();
        $scoreTypes = $media->scores;

        return view('default_view::admin.pages.media.view', compact('media', 'skinScore', 'awesomenessScore', 'scoreTypes'));
    }
}
