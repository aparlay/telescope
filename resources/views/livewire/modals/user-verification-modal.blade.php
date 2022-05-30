@php
    use Aparlay\Core\Models\Enums\UserVerificationStatus;
    use Aparlay\Core\Models\User;
    use \Aparlay\Core\Models\Enums\UserDocumentStatus;
    use Illuminate\Support\Arr;
    use Aparlay\Core\Models\Enums\UserDocumentType;
    use Aparlay\Core\Models\UserDocument;

    $documentVerificationStatus = [
       UserDocumentStatus::APPROVED->value => UserDocumentStatus::APPROVED->label(),
       UserDocumentStatus::REJECTED->value => UserDocumentStatus::REJECTED->label(),
    ];
@endphp

<div class="modal-dialog modal-xl verify-user-modal" role="document">
    <div class="modal-content">
        <div class="modal-header pb-0">
            <h3 class="text-center w-100 mb-0">ID Verification</h3>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
        <div class="modal-body pt-0">
            <div class="alert-default-secondary verify-header p-2">
                <div class="row mt-0">
                    <div class="col-md-6 my-auto">
                        <div class="d-flex">
                            <div class="avatar">
                                <img src="{{ $user->avatar }}?aspect_ratio=1:1&width=150" alt="" class="profile-user-img img-fluid img-circle">
                            </div>

                            <div class="user-details">
                                <a href="{{$user->admin_url}}">
                                    {{ $user->full_name }}
                                </a>
                                <div>
                                    <span class="text-sm">{{ $user->country_label }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 text-right my-auto verification-status">
                        <span class="p-2 text-uppercase badge badge-{{ UserVerificationStatus::from($user->verification_status)->badgeColor()}}">
                            {{ $user->verification_status_label }}
                        </span>
                    </div>
                </div>
            </div>
            @if (count($documents) > 0)
                <div class="documents-list mt-2">
                    <div class="row">
                        @foreach($documents as $document)
                            <div class="col-md-12 pb-3">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <a class="d-block" target="_blank" href="{{ $document->temporaryUrl() }}"
                                               title="{{$document->file}}">
                                                <img class="img-thumbnail" src="{{$document->temporaryUrl()}}" alt="">
                                                {{ $document->file }}
                                            </a>

                                            <span
                                                class="badge badge-{{ UserDocumentStatus::from($document->status)->badgeColor()}}">
                                                    {{ $document->status_label }}
                                            </span>

                                            <div class="text-sm" title="{{ $document->created_at }}">
                                                Uploaded at: {{ $document->created_at->diffForHumans() }}
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="w-100">
                                                <h6>
                                                    @if ($document->type === UserDocumentType::ID_CARD->value)
                                                        1. Photo Id
                                                    @endif
                                                    @if ($document->type === UserDocumentType::SELFIE->value)
                                                        2. Selfie Photo
                                                    @endif
                                                </h6>

                                                <div class="form-check">
                                                    <input class="form-check-input"
                                                           type="radio"
                                                           wire:model="documentsData.{{$document->_id}}.status"
                                                           name="{{ 'document_radio_reject_' . $document->_id }}"
                                                           value="{{ UserDocumentStatus::APPROVED->value }}">

                                                    <label class="form-check-label" for="{{ 'document_radio_reject_' . $document->_id }}">
                                                        Approve
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input
                                                        class="form-check-input"
                                                        wire:model="documentsData.{{$document->_id}}.status"
                                                        value="{{ UserDocumentStatus::REJECTED->value }}"
                                                        name="{{ 'document_radio_reject_' . $document->_id }}"
                                                        type="radio"
                                                        checked>
                                                    <label class="form-check-label" for="{{ 'document_radio_reject_' . $document->_id }}">
                                                        Reject
                                                    </label>
                                                </div>
                                            </div>

                                            @if ((int) Arr::get($documentsData, "$document->_id.status", false) === UserDocumentStatus::REJECTED->value)
                                                <div class="mt-2 w-100 reject-reason">
                                                    <label for="reason">
                                                        Reason *
                                                    </label>
                                                    <textarea type="text"
                                                              wire:model="documentsData.{{$document->_id}}.reason"
                                                              class="form-control"
                                                              placeholder="Reject reason"></textarea>

                                                    @error('documentsData.' . $document->_id . '.reason')
                                                    <span class="text text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            @endif
                                        </div>
                                    </div>
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
            <button type="button" wire:click="save()" class="btn btn-block btn-primary close-modal text-uppercase">Submit</button>
        </div>
    </div>
</div>
