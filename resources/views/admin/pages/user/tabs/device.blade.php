<div class="tab-pane active table-responsive" id="user-info">
    <table class="table table-sm table-hover table-striped">
        <thead class="thead-light">
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
        @php
            $i = 0;
        @endphp
        @foreach($user->user_agents as $index => $userDevice)
            @php
                $i++;

                $db = filter_var($userDevice['ip'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ?
                    database_path().'/ip2location/IP2LOCATION-LITE-DB11.BIN' :
                    database_path().'/ip2location/IP2LOCATION-LITE-DB11.IPV6.BIN';

                $ip2location = (new \IP2Location\Database($db, \IP2Location\Database::FILE_IO))
                ->lookup($userDevice['ip'], \IP2Location\Database::ALL);
                $agent = new \Aparlay\Core\Admin\Services\UserAgent($userDevice['user_agent']);
            @endphp
        <tr>
            <th scope="row">{{ $i }}</th>
            <td>{{$userDevice['device_id']}}</td>
            <td>{{$userDevice['ip']}}</td>
            <td>
                @if (!empty($ip2location['countryCode']))
                <img src="{{ \Aparlay\Core\Helpers\Country::getFlagByAlpha2($ip2location['countryCode'], '24') }}" alt="">
                 {{ $ip2location['countryName'] ?? '' }} {{ $ip2location['countryCode'] ?? '' }}
                @else
                    Unknown!
                @endif
            </td>
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
