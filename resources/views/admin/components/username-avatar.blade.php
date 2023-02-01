@php
    use Aparlay\Core\Models\User;
@endphp

@if ($user instanceof User)
    <div class="x-username-avatar float-left {{$class}}" @class(['mr-2' => empty($class)])>
        <a href="{{$user->admin_url}}"
           title="{{$user->username}} [{{$user->is_online ? 'online' : 'offline'}}] [{{User::getVerificationStatuses()[$user->verification_status] ?? 'none'}}]">
            <img src="{{ $user->avatar }}?aspect_ratio=1:1&width=150" alt="" class="img-circle img-size-32 mr-2">
            {{ $user->username }}
            @if($user->is_online)
                <i title="online" class="ml-1 fas fa-circle text-success text-small" style="font-size: 10px; filter: drop-shadow(0px 0px 3px #28A745);"></i>
            @endif
            @if ($user->is_verified)
                <img src="{{ asset('admin/assets/img/verify-16.png') }}" alt="Verified">
            @endif
        </a>
    </div>
@endif
