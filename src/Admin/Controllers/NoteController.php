<?php

namespace Aparlay\Core\Admin\Controllers;

use Aparlay\Core\Admin\Models\Note;
use Aparlay\Core\Admin\Requests\NoteRequest;
use Aparlay\Core\Admin\Services\NoteService;

class NoteController extends Controller
{
    protected $noteService;

    public function __construct(NoteService $noteService)
    {
        $this->noteService = $noteService;
    }

    /**
     * @throws \ErrorException
     */
    public function index()
    {
    }

    public function view($id)
    {
    }

    public function store(NoteRequest $request)
    {
        dd(request()->input());
    }

    public function delete(Note $note)
    {
    }
}
