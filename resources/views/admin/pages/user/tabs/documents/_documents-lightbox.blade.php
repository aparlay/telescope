<div class="mb-5">
    <h4>{{ $sectionTitle }}</h4>

    <div class="filter-container p-0 row">
        @foreach($documents as $document)
            @include('default_view::admin.pages.user.tabs.documents._document-item', compact('document'))
        @endforeach
    </div>
</div>


@include('default_view::admin.pages.user.tabs.documents._documents-approve_modal')
@include('default_view::admin.pages.user.tabs.documents._documents-reject_modal')

