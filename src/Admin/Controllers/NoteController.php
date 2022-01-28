<?php

namespace Aparlay\Core\Admin\Controllers;

use Aparlay\Core\Admin\Requests\NoteRequest;
use Aparlay\Core\Models\Note;

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

    public function create(NoteRequest $request)
    {
    }

    public function delete(Note $note)
    {
    }
}
