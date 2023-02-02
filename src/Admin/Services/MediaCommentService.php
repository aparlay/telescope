<?php

namespace Aparlay\Core\Admin\Services;

use Aparlay\Core\Admin\Models\MediaComment;

class MediaCommentService
{
    public function __construct()
    {
    }

    public function delete($id)
    {
        return MediaComment::findOrFail($id)->delete();
    }
}
