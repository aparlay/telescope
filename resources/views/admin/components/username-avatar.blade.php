@php
    use Aparlay\Core\Models\User;
@endphp

<div class="x-username-avatar">
    <a href="{{$user->admin_url}}"
       title="{{$user->username}} [{{$user->is_online ? 'online' : 'offline'}}] [{{User::getVerificationStatuses()[$user->verification_status] ?? 'none'}}]">
        <img src="{{ $user->avatar }}?aspect_ratio=1:1&width=150" alt="" class="img-circle img-size-50 mr-2">
        {{$user->username }}
        <i title="{{$user->is_online ? 'online' : 'offline'}}" @class(['fa-user', 'ml-1', 'fas text-success' => $user->is_online, 'far text-gray' => !$user->is_online])></i>
        @if ($user->is_verified)
            <img src="{{ asset('admin/assets/img/verify-16.png') }}" alt="Verified">
        @endif
    </a>
</div>
