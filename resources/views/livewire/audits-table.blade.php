@php
    use Aparlay\Core\Models\Enums\NoteCategory;
@endphp

<div class="">
    <table class="table table-striped border">
        <thead>
            <tr class="d-flex">
                <th class="col-2 col-md-1 col-sm-2">
                    <x-sortable-column-header :sort="$sort" :fieldName="'user'" :fieldLabel="'Created By'"/>
                </th>
                <th class="col-4 col-md-8 col-sm-6">
                    <x-sortable-column-header :sort="$sort" :fieldName="'old_values'" :fieldLabel="'Old value'"/>
                </th>
                <th class="col-4 col-md-8 col-sm-6">
                    <x-sortable-column-header :sort="$sort" :fieldName="'new_values'" :fieldLabel="'New value'"/>
                </th>
                <th class="col-4 col-md-2 col-sm-3">
                    <x-sortable-column-header :sort="$sort" :fieldName="'created_at'" :fieldLabel="'Created At'"/>
                </th>
            </tr>
        </thead>

        <tbody>
            @foreach($audits as $audit)
                <tr class="d-flex">
                    <td class="col-1 col-md-1 col-sm-2">
                        <a href="{{ $audit->user->admin_url }}" target="_blank">
                            {{ $audit->user['username'] }}
                        </a>
                    </td>

                    <td class="col-8 col-md-8 col-sm-6">
                        <code>{!! json_encode($audit->old_values, JSON_PRETTY_PRINT) !!}</code>
                    </td>
                    <td class="col-8 col-md-8 col-sm-6">
                        <code>{!! json_encode($audit->new_values, JSON_PRETTY_PRINT) !!}</code>
                    </td>
                    <td class="col-2 col-md-2 col-sm-3">
                        {{ $audit->created_at }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $audits->links() }}
</div>
