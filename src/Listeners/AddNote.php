<?php

namespace Aparlay\Core\Listeners;

use Aparlay\Core\Api\V1\Services\NoteService;

class AddNote
{   


    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(int $type, array $user)
    {
        $this->type = $type;
        $this->user = $user;
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function handle($event)
    {
        if (auth()->check()) {
            $noteService = app()->make(NoteService::class);
            $noteService->addNewNote(
                auth()->user(),
                $this->user,
                $this->type,
            );
        }
    }
}
