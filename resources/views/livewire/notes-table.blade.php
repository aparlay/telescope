<div class="notes-table">
    <h4>Notes</h4>

    <div class="filters pb-2">
        <div class="row">
            <div class="col-md-12">
                <button class="btn btn-success" type="button"
                        wire:click="$emit('showModal', 'modals.user-notes-create-modal', '{{ $userId }}')">
                    Add New <i class="fas fa-plus fa-xs"></i>
                </button>
            </div>
<!--
            <div class="col-md-2">
                <label for="">Start Date</label>
                <x-date-picker
                        wire:model.lazy="filter.created_at.start"
                        autocomplete="off"
                        placeholder="Start"
                />
            </div>
            <div class="col-md-2">
                <label for="">End Date</label>
                <x-date-picker
                        wire:model.lazy="filter.created_at.end"
                        autocomplete="off"
                        placeholder="End"
                />
            </div>
            <div class="col-md-2 ml-auto">
                <label for="">Per Page</label>
                <x-wire-dropdown-list :wire-model="'perPage'" :show-any="false" :options="[5 => 5, 10 => 10, 15 => 15]"/>
            </div>
-->
        </div>
    </div>

    <table class="table table-striped">
        <tbody>
        <tr>
            <th class="col-md-2">
                <div>
                    <x-sortable-column-header :sort="$sort" :fieldName="'created_by'" :fieldLabel="'Created By'"/>
                    <!--<input class="form-control" type="text" wire:model="filter.creator_username"/>-->
                </div>
            </th>
            <th class="col-md-6">
                <div>
                    <x-sortable-column-header :sort="$sort" :fieldName="'message'" :fieldLabel="'Notes'"/>
                    <input class="form-control" type="text" wire:model="filter.message"/>
                </div>
            </th>
            <td class="col-md-1">
                <div>
                    <x-sortable-column-header :sort="$sort" :fieldName="'type'" :fieldLabel="'Type'" />
                    <select class="form-control" wire:model="filter.type">
                        <option value="">Any</option>
                        @foreach(\Aparlay\Core\Models\Note::getTypes() as $value => $label)
                            <option value="{{$value}}">{{$label}}</option>
                        @endforeach
                    </select>
                </div>
            </td>
            <th class="col-md-2">
                <div>
                    <x-sortable-column-header :sort="$sort" :fieldName="'created_at'" :fieldLabel="'Created At'"/>
                </div>
            </th>
            <th class="col-md-1">
                <div>
                    <label for="">Action</label>
                </div>
            </th>
        </tr>

        @foreach($notes as $note)
            <tr>
                <td>
                    <a href="{{ $note->creatorObj->admin_url }}" target="_blank">
                        {{ $note->creator['username'] }}
                    </a>
                </td>
                <td>
                    {!! $note->message !!}
                </td>
                <td>
                    <span class="badge bg-{{ \Aparlay\Core\Models\Enums\NoteType::from($note->type)->badgeColor() }}">
                        {{ \Aparlay\Core\Models\Enums\NoteType::from($note->type)->label() }}
                    </span>
                </td>
                <td>
                    {{ $note->created_at }}
                </td>
                <td>
                    <div>
                        <a
                            class=""
                            wire:key="delete_note_{{ $note->_id }}}"
                            wire:click="$emit('showModal', 'modals.user-notes-delete-modal', '{{ $note->_id }}')"
                        >
                            <i class="fa fa-fw fa-trash"></i>
                        </a>
                    </div>
                </td>
            </tr>

        @endforeach
        </tbody>
    </table>
    {{ $notes->links() }}
</div>
