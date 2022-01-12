<div class="tab-pane active" id="user-info">
    <table class="table table-hover">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Device ID</th>
            <th scope="col">IP</th>
            <th scope="col">Country</th>
            <th scope="col">Device</th>
            <th scope="col">OS</th>
            <th scope="col">Browser</th>
            <th scope="col">Version</th>
            <th scope="col">Last Use</th>
        </tr>
        </thead>
        <tbody>
        @foreach($user->user_agents as $index => $userDevice)
            @php
                $ip2location = (new \IP2Location\Database(database_path().'/ip2location/IP2LOCATION-LITE-DB11.BIN', \IP2Location\Database::FILE_IO))
                ->lookup($userDevice['ip'], \IP2Location\Database::ALL);
                $agent = new \SimpleUserAgent\UserAgent($userDevice['user_agent']);
            @endphp
        <tr>
            <th scope="row">1</th>
            <td>{{$userDevice['device_id']}}</td>
            <td>{{$userDevice['ip']}}</td>
            <td><img src="{{ \Aparlay\Core\Helpers\Country::flagFromAlpha2($ip2location['countryCode']) }}" alt="">
                 {{ $ip2location['countryName'] ?? '' }} {{ $ip2location['countryCode'] ?? '' }}</td>
            <td>{{ $agent->getDevice() }}</td>
            <td>{{ $agent->getOS() }}</td>
            <td>{{ $agent->getBrowser() }}</td>
            <td>{{ $agent->getVersion() }}</td>
            <td>{{ \Carbon\Carbon::createFromTimestampMsUTC($userDevice['created_at']) }}</td>
        </tr>
        @endforeach
        </tbody>
    </table>

</div>
