<?php

namespace Aparlay\Core\Admin\Services;

use Aparlay\Core\Admin\Models\Note;
use Aparlay\Core\Admin\Repositories\NoteRepository;

class NoteService
{
    protected NoteRepository $noteRepository;

    public function __construct()
    {
        $this->noteRepository = new NoteRepository(new Note());
    }

    public function create()
    {
        return $this->noteRepository->create(request()->only(['user_id', 'type', 'message']));
    }
}
