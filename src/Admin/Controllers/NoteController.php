<?php

namespace Aparlay\Core\Admin\Controllers;

use Aparlay\Core\Models\Note;

use Aparlay\Core\Admin\Requests\NoteRequest;

class NoteController extends Controller
{
    protected $alertService;

    public function __construct(AlertService $alertService)
    {
        $this->alertService = $alertService;
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

    public function delete(Note $note)
    {

    }

    public function create(NoteRequest $request)
    {
        dd(1212312);
    }
}
