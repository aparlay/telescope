<?php

namespace Aparlay\Core\Admin\Services;

use Illuminate\Http\Request;

class AlertService
{
    protected MediaRepository $mediaRepository;

    public function __construct()
    {
        $this->mediaRepository = new MediaRepository(new Media());
    }
}
