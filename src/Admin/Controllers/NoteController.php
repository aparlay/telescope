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
        $create = $this->noteService->create();
        return redirect()->route('core.admin.user.view', ['user' => $create->user['_id']])->with([
            'success' => 'Successfully added notes.',
        ]);
    }

    public function delete(Note $note)
    {
        if ($this->noteService->delete($note->_id)) {
            return back()->with(['success' => 'Successfully deleted note']);
        } else {
            return back()->with(['error' => 'Delete note failed']);
        }     
    }
}
