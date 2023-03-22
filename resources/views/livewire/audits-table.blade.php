<div>
    <table class="table table-responsive table-striped border">
        <thead>
            <tr class="d-flex">
                <th scope="col" class="col-2 col-md-2 col-sm-3">
                    <x-sortable-column-header :sort="$sort" :fieldName="'user'" :fieldLabel="'Created By'"/>
                </th>
                <th scope="col" class="col-4 col-md-4 col-sm-6">
                    <x-sortable-column-header :sort="$sort" :fieldName="'old_values'" :fieldLabel="'Old value'"/>
                </th>
                <th scope="col" class="col-4 col-md-4 col-sm-6">
                    <x-sortable-column-header :sort="$sort" :fieldName="'new_values'" :fieldLabel="'New value'"/>
                </th>
                <th scope="col" class="col-4 col-md-2 col-sm-3">
                    <x-sortable-column-header :sort="$sort" :fieldName="'created_at'" :fieldLabel="'Created At'"/>
                </th>
            </tr>
        </thead>

        <tbody>
            @foreach($audits as $audit)
                <tr class="d-flex">
                    <td class="col-2 col-md-2 col-sm-3">
                        <a href="{{ $audit->user->admin_url }}" target="_blank">
                            {{ $audit->user['username'] }}
                        </a>
                    </td>

                    <td class="col-4 col-md-4 col-sm-6">
                        <code>{!! nl2br($audit->parsed_old) !!}</code>
                    </td>
                    <td class="col-4 col-md-4 col-sm-6">
                        <code>{!! nl2br($audit->parsed_new) !!}</code>
                    </td>
                    <td class="col-2 col-md-2 col-sm-3">
                        {{ $audit->created_at }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="d-flex justify-content-center">
        {{ $audits->links() }}
    </div>
</div>
