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
    public function index(Request $request)
    {
        $breadcrumbs = [
            'title' => 'Media'
        ];

        $media_list = $this->mediaService->getList();

        return view('default_view::admin.pages.media.index')->with([
            'media_list'  => $media_list,
            'breadcrumbs' => $breadcrumbs,
        ]);
    }
}
