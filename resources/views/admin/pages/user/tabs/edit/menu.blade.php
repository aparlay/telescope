@php
    use Aparlay\Core\Models\Enums\UserStatus;
    use Aparlay\Core\Models\Enums\UserVisibility;
    use Aparlay\Chat\Models\Enums\ChatCategory;
    use Aparlay\Chat\Admin\Models\Chat;
    use Aparlay\Core\Models\Enums\UserVerificationStatus;
@endphp

<div class="row text-center">
    <img src="{{ $user->avatar }}?aspect_ratio=1:1&width=150" alt="" class="img-fluid w-100">
</div>

<div class="row">
    <div class="col-12 mb-2">
        <span class="mb-2 text-muted"><b>{{ "@" . $user->username }}</b>
            @if($user->verification_status === UserVerificationStatus::VERIFIED->value)
                <img src="{{ asset('admin/assets/img/verify-16.png') }}" alt="Verified">
            @endif
        </span>
    </div>
    <div class="col-6 border-right" style="font-size: 14px">
        <span class="text-muted text-small">Likes: <span class="float-right">{{ $user->stats['counters']['likes'] ?? 0 }}</span></span><br>
        <hr class="my-1">
        <span class="text-muted text-small">Following: <span class="float-right">{{ $user->stats['counters']['following'] ?? 0 }}</span></span><br>
        <hr class="my-1">
        <span class="text-muted text-small">Followers: <span class="float-right">{{ $user->stats['counters']['followers'] ?? 0 }}</span></span><br>
    </div>
    <div class="col-6" style="font-size: 14px;">
        <span class="text-muted text-small">Tips Out: <span class="float-right">{{ money((int)($user->stats['amounts']['spent']['tips'] ?? 0), 'USD') }}</span></span><br>
        <hr class="my-1">
        <span class="text-muted text-small">Tips In: <span class="float-right">{{ money((int)($user->stats['amounts']['spent']['tips'] ?? 0), 'USD') }}</span></span><br>
        <hr class="my-1">
        <span class="text-muted text-small">Payouts: <span class="float-right">{{ money((int)($user->stats['amounts']['earned']['referral']['subscriptions'] ?? 0), 'USD') }}</span></span>
    </div>
</div>

<div class="row card card-default list-group">
    <a href="{{ route('chat.admin.chat.chat-as-support', ['user' => $user->_id]) }}" class="py-1 px-2 list-group-item list-group-item-action"><i class="fas fa-circle mr-1 text-blue"></i>Chat As Support</a>
    <a href="{{ route('core.admin.user.login_as_user', ['user' => $user->_id]) }}" target="_blank" class="py-1 px-2 list-group-item list-group-item-action">
        <i class="fas fa-circle mr-1 text-blue"></i>Log-In As User
    </a>
    <a href="#" class="py-1 px-2 list-group-item list-group-item-action" data-toggle="modal" data-target="#changePasswordModal">
        <i class="fas fa-circle mr-1 text-blue"></i>Set Password
    </a>
    <!--<a href="#" class="py-1 px-2 list-group-item list-group-item-action disabled"><i class="fas fa-circle mr-1 text-blue"></i>Gallery</a>-->
    <!--<a href="#" class="py-1 px-2 list-group-item list-group-item-action disabled"><i class="fas fa-circle mr-1 text-blue"></i>Adjustments</a>-->
    <a href="{{ route('chat.admin.chat.index', ['userId' => $user->_id]) }}" class="py-1 px-2 list-group-item list-group-item-action">
        <i class="fas fa-circle mr-1 text-blue"></i>View Chats ({{ Chat::query()->participants((string) $user->_id)->count() }})
    </a>
    <!--<a href="#" class="py-1 px-2 list-group-item list-group-item-action disabled"><i class="fas fa-circle mr-1 text-blue"></i>Un-Hide Chats</a>-->
    <a href="#" class="py-1 px-2 list-group-item list-group-item-action disabled"><i class="fas fa-circle mr-1 text-blue"></i>Flush Unread Messages</a>
    <a href="#" class="py-1 px-2 list-group-item list-group-item-action disabled"><i class="fas fa-circle mr-1 text-blue"></i>Logs</a>
</div>

<div class="row card card-default list-group">
    @if(config('app.enabled-features.user.warning-message'))
        <a href="#" class="py-1 px-2 list-group-item list-group-item-action" data-toggle="modal" data-target="#alertModal">
            <i class="fas fa-circle mr-1 text-blue"></i>Give Warning
        </a>
    @endif
    <a href="#" class="py-1 px-2 list-group-item list-group-item-action disabled"><i class="fas fa-circle mr-1 text-blue"></i>Give Warning</a>
    <!--<a href="#" class="py-1 px-2 list-group-item list-group-item-action disabled"><i class="fas fa-circle mr-1 text-blue"></i>Give Final Warning</a>-->
    <a href="#" class="py-1 px-2 list-group-item list-group-item-action disabled"><i class="fas fa-circle mr-1 text-blue"></i>Validation Freeze</a>
    <!--<a href="#" class="py-1 px-2 list-group-item list-group-item-action disabled"><i class="fas fa-circle mr-1 text-blue"></i>Freeze</a>-->
    @if($user->status == UserStatus::SUSPENDED->value)
        <a href="#" class="py-1 px-2 list-group-item list-group-item-action" data-toggle="modal" data-target="#activateModal">
            <i class="fas fa-circle mr-1 text-blue"></i>Reactivate
        </a>
    @else
        <a href="#" class="py-1 px-2 list-group-item list-group-item-action" data-toggle="modal" data-target="#suspendModal">
            <i class="fas fa-circle mr-1 text-blue"></i>Suspend
        </a>
    @endif
    <!--<a href="#" class="py-1 px-2 list-group-item list-group-item-action disabled"><i class="fas fa-circle mr-1 text-blue"></i>Ban Send Photo</a>-->
    <!--<a href="#" class="py-1 px-2 list-group-item list-group-item-action disabled"><i class="fas fa-circle mr-1 text-blue"></i>Ban Intro</a>-->
    @if(false == $user->setting['payout']['auto_ban_payout'])
        <a href="#" class="py-1 px-2 list-group-item list-group-item-action disabled" data-toggle="modal" data-target="#set-auto-ban-payout-modal">
            <i class="fas fa-circle mr-1 text-blue"></i>Set Auto Ban Payout
        </a>
    @else
        <a href="#" class="py-1 px-2 list-group-item list-group-item-action disabled" data-toggle="modal" data-target="#unset-auto-ban-payout-modal">
            <i class="fas fa-circle mr-1 text-blue"></i>Unset Auto Ban Payout
        </a>
    @endif
    @if(false == $user->setting['payout']['ban_payout'])
        <a href="#" class="py-1 px-2 list-group-item list-group-item-action disabled" data-toggle="modal" data-target="#set-ban-payout-modal">
            <i class="fas fa-circle mr-1 text-blue"></i>Set Ban Payout
        </a>
    @else
        <a href="#" class="py-1 px-2 list-group-item list-group-item-action disabled" data-toggle="modal" data-target="#unset-ban-payout-modal">
            <i class="fas fa-circle mr-1 text-blue"></i>Unset Ban Payout
        </a>
    @endif
    <!--<a href="#" class="py-1 px-2 list-group-item list-group-item-action disabled"><i class="fas fa-circle mr-1 text-blue"></i>Enable 14 Day Payout Freeze</a>-->
    <!--<a href="#" class="py-1 px-2 list-group-item list-group-item-action disabled"><i class="fas fa-circle mr-1 text-blue"></i>Region Blacklist</a>-->
    <!--<a href="#" class="py-1 px-2 list-group-item list-group-item-action disabled"><i class="fas fa-circle mr-1 text-blue"></i>Allow Screenshots</a>-->
    <a href="#" class="py-1 px-2 list-group-item list-group-item-action text-red disabled"><i class="fas fa-circle mr-1 text-red"></i>Delete Account</a>
    <a href="#" class="py-1 px-2 list-group-item list-group-item-action text-red disabled"><i class="fas fa-circle mr-1 text-red"></i>Delete Account w/ Timer</a>
    @if($user->status == UserStatus::BLOCKED->value)
        <a href="#" class="py-1 px-2 list-group-item list-group-item-action text-green" data-toggle="modal" data-target="#activateModal">
            <i class="fas fa-circle mr-1 text-green"></i>Reactivate
        </a>
    @else
        <a href="#" class="py-1 px-2 list-group-item list-group-item-action text-red" data-toggle="modal" data-target="#banModal">
            <i class="fas fa-circle mr-1 text-red"></i>Ban Account
        </a>
    @endif
    <a href="#" class="py-1 px-2 list-group-item list-group-item-action text-red disabled"><i class="fas fa-circle mr-1 text-red"></i>Ban Device Fingerprint</a>
</div>

<h5 class="row text-gray text-uppercase">Model</h5>
<div class="row card card-default list-group">
    <a href="#" class="py-1 px-2 list-group-item list-group-item-action disabled"><i class="fas fa-circle mr-1 text-blue"></i>Reset New Status</a>
    <a href="#" class="py-1 px-2 list-group-item list-group-item-action disabled"><i class="fas fa-circle mr-1 text-blue"></i>Enable Red Visibility Warning</a>
    <a href="#" class="py-1 px-2 list-group-item list-group-item-action disabled"><i class="fas fa-circle mr-1 text-blue"></i>Ban new Chats with Models</a>
    <a href="#" class="py-1 px-2 list-group-item list-group-item-action disabled"><i class="fas fa-circle mr-1 text-blue"></i>Ledger</a>
    <a href="#" class="py-1 px-2 list-group-item list-group-item-action disabled"><i class="fas fa-circle mr-1 text-blue"></i>Ledger Export</a>
    <a href="#" class="py-1 px-2 list-group-item list-group-item-action disabled"><i class="fas fa-circle mr-1 text-blue"></i>Request Payout</a>
</div>

<h5 class="row text-gray text-uppercase">Fraud</h5>
<div class="row card card-default list-group">
    <a href="#" class="py-1 px-2 list-group-item list-group-item-action disabled"><i class="fas fa-circle mr-1 text-blue"></i>Ban All CC Payments</a>
    <a href="#" class="py-1 px-2 list-group-item list-group-item-action disabled"><i class="fas fa-circle mr-1 text-blue"></i>Block Un-Verified CCs</a>
    <a href="#" class="py-1 px-2 list-group-item list-group-item-action disabled"><i class="fas fa-circle mr-1 text-blue"></i>Send CC Verification Request</a>
    <a href="#" class="py-1 px-2 list-group-item list-group-item-action disabled"><i class="fas fa-circle mr-1 text-blue"></i>CC Whitelist</a>
    <a href="#" class="py-1 px-2 list-group-item list-group-item-action disabled"><i class="fas fa-circle mr-1 text-blue"></i>3DS</a>
</div>

<h5 class="row text-gray text-uppercase">Admin Tools</h5>
<div class="row card card-default list-group">
    <a href="#" class="py-1 px-2 list-group-item list-group-item-action disabled"><i class="fas fa-circle mr-1 text-blue"></i>Reviewer Account</a>
    <a href="#" class="py-1 px-2 list-group-item list-group-item-action disabled"><i class="fas fa-circle mr-1 text-blue"></i>Role</a>
    <a href="#" class="py-1 px-2 list-group-item list-group-item-action disabled"><i class="fas fa-circle mr-1 text-blue"></i>Is Staff Mo</a>
    <a href="#" class="py-1 px-2 list-group-item list-group-item-action disabled"><i class="fas fa-circle mr-1 text-blue"></i>Debug Enabled</a>
</div>


<h5 class="row text-gray text-uppercase">Test Tools</h5>
<div class="row card card-default list-group">
    <a href="#" class="py-1 px-2 list-group-item list-group-item-action text-green" data-toggle="modal" data-target="#pushNotificationsModal"><i class="fas fa-circle mr-1 text-blue"></i>Push Notification</a>
</div>
