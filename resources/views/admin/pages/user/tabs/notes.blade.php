@php
    use Aparlay\Core\Models\Enums\NoteCategory;
@endphp

<div class="notes-table user-profile-table">
    <div class="pb-2">
        <div class="row">
            <div class="col-12 col-lg-1 pt-2 pl-3">
                <span class="h5">Notes</span>
            </div>

            <div class="col-12 col-lg-11">
                <livewire:notes-create :userId="$user->_id"/>
            </div>
        </div>
    </div>

    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#notes-notes">Notes</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#notes-logs">Logs</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#notes-audit">Audit</a>
        </li>
    </ul>


    <div class="tab-content">
        <div class="tab-pane container active" id="notes-notes">
            <livewire:notes-table :userId="$user->_id" :category="NoteCategory::NOTE->value"/>
        </div>
        <div class="tab-pane container fade" id="notes-logs">
            <livewire:notes-table :userId="$user->_id" :category="NoteCategory::LOG->value"/>
        </div>
        <div class="tab-pane container fade" id="notes-audit">
            <livewire:audits-table :auditableType="'User'" :auditableId="(string) $user->_id"/>
        </div>
    </div>
</div>
