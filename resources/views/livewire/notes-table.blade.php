
<div class="notes-table">
<div class="filters pb-3">
        <div class="row">
            <div class="col-md-9 pt-4">
                <h4>Emails</h4>
            </div>
            <div class="col-md-1">
                <label for="">Start Date</label>
                <x-date-picker
                        wire:model.lazy="filter.created_at.start"
                        autocomplete="off"
                        placeholder="Start"
                />
            </div>
            <div class="col-md-1">
                <label for="">End Date</label>
                <x-date-picker
                        wire:model.lazy="filter.created_at.end"
                        autocomplete="off"
                        placeholder="End"
                />
            </div>
            <div class="col-md-1 ml-auto">
                <label for="">Per Page</label>
                <x-wire-dropdown-list :wire-model="'perPage'" :show-any="false" :options="[5 => 5, 10 => 10, 15 => 15]"/>
            </div>
        </div>
    </div>

    <table class="table table-striped">
        <tbody>
        <tr>
            <th class="col-md-3">
                <div>
                   
                    <x-sortable-column-header :sort="$sort" :fieldName="'created_by'" :fieldLabel="'Created By'" />
                    <input class="form-control" type="text" wire:model="filter.creator_username"/>    
                </div>
            </th>
            <th class="col-md-3">
                <div>
                    <x-sortable-column-header :sort="$sort" :fieldName="'message'" :fieldLabel="'Notes'" />
                    <input class="form-control" type="text" wire:model="filter.message"/>    
                </div>
            </th>
            <th class="col-md-3">
                <div>
                    <x-sortable-column-header :sort="$sort" :fieldName="'created_at'" :fieldLabel="'Created At'" />
                </div>
            </th>
            <th class="col-md-3">
                <div>
                    <label for="">Action</label>

                </div>
            </th>
            <th></th>
        </tr>

        @foreach($notes as $note)
            <tr>
                <td>
                    <a href="">
                      
                    {{ $note->creator['username'] }}
                       
                    </a>
                </td>
              
                <td>
                    {{ $note->message }}
                </td>
                <td>
                    {{ $note->created_at }}
                </td>
                <td>
                    <div>
                        <button type="submit" class="btn btn-danger" data-toggle="modal" data-target="#deleteNote-{{$note->_id}}">Delete</button>
                    </div>
                </td>
            </tr>

            <div id="deleteNote-{{$note->_id}}" class="modal fade" role="dialog">
                <div class="modal-dialog">
                    <!-- Modal content-->
                    <div class="modal-content">
                        <form action="{{ route('core.admin.note.delete', ['note' => $note->_id])  }}"
                              method="POST">
                            @csrf
                            @method('delete')
                            <div class="modal-header bg-danger">
                                <h5 class="modal-title" id="exampleModalLiveLabel">Delete User Note</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <p>Are you sure you want to delete this note?</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

                                <button type="submit" class="btn btn-danger">Delete</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
        </tbody>
    </table>
    {{ $notes->links() }}
</div>
