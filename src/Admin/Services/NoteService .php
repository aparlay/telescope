<?php

namespace Aparlay\Core\Admin\Services;

use Aparlay\Core\Models\Note;
use Aparlay\Core\Admin\Repositories\NoteRepository;

class AlertService
{
    protected NoteRepository $noteRepository;
   

    public function __construct()
    {
        $this->noteRepository = new NoteRepository(new Note());
        
    }

    public function create()
    {
       
    }
}
