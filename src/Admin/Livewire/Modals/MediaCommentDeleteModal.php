<?php

namespace Aparlay\Core\Admin\Livewire\Modals;

use Aparlay\Core\Admin\Livewire\Traits\CurrentUserTrait;
use Aparlay\Core\Admin\Models\MediaComment;
use Livewire\Component;

class MediaCommentDeleteModal extends Component
{
    use CurrentUserTrait;
    public $selectedItem;
    public $mediaComment;

    public function mount($mediaCommentId)
    {
        $this->selectedItem = $mediaCommentId;
        $this->mediaComment = MediaComment::find($mediaCommentId);
    }

    public function delete()
    {
        $currentUser = $this->currentUser();
        if ($currentUser->can('delete media-comments')) {
            $mediaComment = MediaComment::find($this->selectedItem);
            $mediaComment->delete();
        }

        $this->dispatchBrowserEvent('hideModal');
        $this->emit('updateParent');
    }

    public function render()
    {
        return view('default_view::livewire.modals.media-comment-delete-modal');
    }
}
