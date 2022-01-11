@php
    /** @var \Aparlay\Core\Api\V1\Models\UserDocument $document */
    /** @var \Aparlay\Core\Models\User $user */
    $label = \Aparlay\Core\Models\Enums\UserDocumentStatus::from($document->status)->name;
    $badgeColor = 'badge badge-' . \Aparlay\Core\Models\Enums\UserDocumentStatus::from($document->status)->badgeColor();
    $documentType = $document->typeLabel;
@endphp

<div class="filtr-item col-sm-2" data-category="1">

    <a href="{{$document->temporaryUrl() }}" data-toggle="lightbox" data-title="{{ $document->file }}">
        <img src="{{$document->temporaryUrl() }}" class="img-fluid mb-2" alt="white sample"/>
    </a>
    <h6>{{$document->file}}</h6>
    <span class="badge badge-secondary">{{$documentType}}</span>
    <span class="{{$badgeColor}}">{{$label}}</span>

    <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-id="{{$document->_id}}" data-target="#approveModal">
        <i class="fas fa-check"></i>
        <strong>Approve</strong>
    </button>

    <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-id="{{$document->_id}}" data-target="#rejectModal">
        <i class="fas fa-times"></i>
        <strong>Reject</strong>
    </button>

    @if($document->status === \Aparlay\Core\Models\Enums\UserDocumentStatus::REJECTED->value)
        <p><b>Reject Reason:</b> {{$document->reject_reason}}</p>
    @endif
</div>
