<?php

namespace Aparlay\Core\Admin\Controllers;

use Aparlay\Core\Admin\Services\MediaService;

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
        $user = $this->mediaService->find($id);

        return view('default_view::admin.pages.media.view', compact('media'));
    }
}
