@php
    use Aparlay\Core\Models\Enums\UserVerificationStatus;
    use Aparlay\Core\Models\User;
    use \Aparlay\Core\Models\Enums\UserDocumentStatus;
    use Illuminate\Support\Arr;
    use Aparlay\Core\Models\Enums\UserDocumentType;

    $documentVerificationStatus = [
       UserDocumentStatus::APPROVED->value => UserDocumentStatus::APPROVED->label(),
       UserDocumentStatus::REJECTED->value => UserDocumentStatus::REJECTED->label(),
    ];
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
            <div>
                Verification status for <span class="badge badge-info">{{ $user->username }}</span>
                is
                <span class="badge badge-{{ UserVerificationStatus::from($user->verification_status)->badgeColor()}}">
                    {{ $user->verification_status_label }}
                </span>
            </div>

            <div>
                <label for="">Verification Status</label>
                <select class="form-control" wire:model="verification_status">
                    @foreach(UserVerificationStatus::getAllCases() as $value => $label)
                        <option value="{{$value}}">{{$label}}</option>
                    @endforeach
                </select>

                @error('verification_status')
                    <div class="text text-danger">{{ $message }}</div>
                @enderror
            </div>

            @if (count($documents) > 0)
                <div class="documents-list mt-2">
                    <div class="row">
                        @foreach($documents as $document)
                            @if($document instanceof \Aparlay\Core\Models\UserDocument)
                                <div class="col-md-4 pb-3">
                                    <div class="w-100">
                                        @if($document->type === UserDocumentType::ID_CARD->value)
                                            <a target="_blank" href="{{ $document->temporaryUrl() }}"
                                               title="{{$document->file}}">
                                                <img class="img-thumbnail" src="{{$document->temporaryUrl()}}" alt="">
                                                {{ $document->file }}
                                                <span
                                                    class="badge badge-{{ UserDocumentStatus::from($document->status)->badgeColor()}}">
                                                {{ $document->status_label }}
                                                </span>
                                            </a>
                                        @endif

                                        @if ($document->type === UserDocumentType::SELFIE->value):
                                            <video width="100%" controls poster="{{ '' }}" style="max-height:400px">
                                                @if ($document->temporaryUrl())
                                                    <source src="{{ $document->temporaryUrl() }}">
                                                @endif
                                                Your browser does not support the video tag.
                                            </video>
                                        @endif


                                        <div class="text-sm" title="{{ $document->created_at }}">
                                            Uploaded at: {{ $document->created_at->diffForHumans() }}
                                        </div>
                                    </div>

                                    <div class="w-100">
                                        <select
                                            id="{{ 'wire_dropdown_'  . uniqid() }}"
                                            class="form-control"
                                            wire:key="{{ uniqid() }}"
                                            wire:model="documentsData.{{$document->_id}}.status">
                                            @foreach($documentVerificationStatus as $value => $label)
                                                <option value="{{$value}}">{{$label}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    @if ((int) Arr::get($documentsData, "$document->_id.status", false) === UserDocumentStatus::REJECTED->value)
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
                            @endif
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
