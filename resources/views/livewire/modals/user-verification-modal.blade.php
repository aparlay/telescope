@php
    use Aparlay\Core\Models\Enums\UserVerificationStatus;
    use Aparlay\Core\Models\User;
    use \Aparlay\Core\Models\Enums\UserDocumentStatus;
    use Illuminate\Support\Arr;
@endphp

<div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Verify user</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true close-btn">×</span>
            </button>
        </div>
        <div class="modal-body">
            <p>Update verification status for <span class="badge badge-info">{{ $user->username }}</p></span>

            <div>
                <label for="">Verification Status</label>
                <select class="form-control" wire:model="verification_status">
                    @foreach(User::getVerificationStatuses() as $value => $label)
                        <option value="{{$value}}">{{$label}}</option>
                    @endforeach
                </select>
                @error('verification_status')
                <div class="text text-danger">{{ $message }}</div> @enderror
            </div>

            @if (count($documents) > 0)
                <div class="documents-list mt-2">
                    <div class="row">
                        @foreach($documents as $document)
                            <div class="col-md-4">
                                <div class="w-100">
                                    <a target="_blank" href="{{ $document->temporaryUrl() }}"
                                       title="{{$document->file}}">
                                        <img class="img-thumbnail" src="{{$document->temporaryUrl()}}" alt="">
                                        {{ $document->file }}
                                        <span
                                            class="badge badge-{{ UserDocumentStatus::from($document->status)->badgeColor()}}">
                                            {{ $document->status_label }}
                                        </span>
                                    </a>
                                </div>


                                <div class="w-100">
                                    <div class="custom-control custom-switch">
                                        <input
                                            type="checkbox" wire:model="documentsData.{{$document->_id}}.is_approved"
                                            class="custom-control-input"
                                            id="{{ 'switcher_' . $document->_id }}">

                                        <label class="custom-control-label" for="{{ 'switcher_' . $document->_id }}">
                                            Reject / Approve
                                        </label>
                                    </div>
                                </div>

                                @if (!Arr::get($documentsData, "$document->_id.is_approved", false))
                                    <div class="mt-2 w-100">
                                        <input type="text"
                                               wire:model="documentsData.{{$document->_id}}.reason"
                                               class="form-control"
                                               placeholder="Reject reason">

                                        @error('documentsData.' . $document->_id . '.reason')
                                        <span class="text text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                </div>
            @else
                <div class="mt-2">
                    <div class="alert alert-secondary">
                        User haven't uploaded any selfies or credit cards documents yet.
                    </div>
                </div>
            @endif
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary close-btn" data-dismiss="modal">Close</button>

            <button type="button" wire:click="save()" class="btn btn-primary close-modal">Save</button>
        </div>
    </div>
</div>
