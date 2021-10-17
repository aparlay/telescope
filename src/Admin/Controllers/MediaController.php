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
}
