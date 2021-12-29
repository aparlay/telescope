@php
    use Aparlay\Core\Models\Enums\UserDocumentStatus;
@endphp

<div class="tab-pane" id="documents">
    <div class="content">
        <div class="container-fluid">
            <div class="row">

                <h4>User Documents</h4>

                <div class="filter-container p-0 row">

                    @foreach($user->userDocumentObjs as $document)
                        @php
                            /** @var \Aparlay\Core\Api\V1\Models\UserDocument $document */
                            /** @var \Aparlay\Core\Models\User $user */
                            $label = UserDocumentStatus::from($document->status)->name;
                            $badgeColor = 'badge badge-' . UserDocumentStatus::from($document->status)->badgeColor();
                            $documentType = \Aparlay\Core\Models\Enums\UserDocumentType::from($document->type)->name;
                        @endphp
                        <div class="filtr-item col-sm-2" data-category="1">

                            <a href="{{$document->temporaryUrl() }}" data-toggle="lightbox" data-title="{{ $documentType }}">
                                <img src="{{$document->temporaryUrl() }}" class="img-fluid mb-2" alt="white sample"/>
                            </a>
                            {{$document->file}}
                            <span class="badge badge-secondary">{{$documentType}}</span>
                            <span class="{{$badgeColor}}">{{$label}}</span>
                        </div>
                    @endforeach

                </div>
            </div>
        </div>
    </div>
</div>
