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

    public function view(MediaComment $comment)
    {
        return view('default_view::admin.pages.media-comment.view', compact('comment'));
    }

    /**
     * @throws \ErrorException
     */
    public function delete(MediaComment $comment)
    {
        $this->mediaCommentService->delete($comment->_id);

        return redirect()->route('core.admin.media.view', ['media' => $comment->mediaObj->_id]);
    }
}
