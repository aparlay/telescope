<?php

namespace Aparlay\Core\Admin\Controllers;

use Aparlay\Core\Admin\Models\MediaComment;
use Aparlay\Core\Admin\Services\MediaCommentService;

class MediaCommentController extends Controller
{
    protected $mediaCommentService;

    public function __construct(MediaCommentService $mediaCommentService)
    {
        $this->mediaCommentService = $mediaCommentService;
    }

    /**
     * @throws \ErrorException
     */
    public function index()
    {
    }

    public function view(MediaComment $comment)
    {
        return view('default_view::admin.pages.media-comment.view', compact('comment'));
    }
}
