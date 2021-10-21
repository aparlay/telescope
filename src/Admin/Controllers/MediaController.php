<?php

namespace Aparlay\Core\Admin\Controllers;

use Aparlay\Core\Admin\Services\MediaService;
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
}
