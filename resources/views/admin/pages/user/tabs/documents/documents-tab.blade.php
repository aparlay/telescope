@php
    use Aparlay\Core\Models\Enums\UserDocumentStatus;
    use Aparlay\Core\Models\Enums\UserDocumentType;
    use Aparlay\Core\Models\UserDocument;

    /** @var User $user */
    $documentsSelfies = $user->userDocumentObjs()->type(UserDocumentType::SELFIE->value)->get();
    $documentsIdCards = $user->userDocumentObjs()->type(UserDocumentType::ID_CARD->value)->get();

    $countSelfies = $documentsSelfies->count();
    $countDocuments = $documentsIdCards->count();

@endphp

<div class="tab-pane" id="documents">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <h4>User Documents</h4>
                @if ($countSelfies > 0)
                    @include('default_view::admin.pages.user.tabs.documents._documents-lightbox', ['documents' => $documentsSelfies, 'sectionTitle' => 'Selfies' ])
                @endif

                @if ($countDocuments > 0)
                    @include('default_view::admin.pages.user.tabs.documents._documents-lightbox', ['documents' => $documentsIdCards, 'sectionTitle' => 'Id cards' ])
                @endif
            </div>

            @if(($countSelfies + $countDocuments) === 0)
                <div class="row">
                    <h6>User haven't uploaded any documents to verify</h6>
                </div>
            @endif
        </div>
    </div>
</div>


