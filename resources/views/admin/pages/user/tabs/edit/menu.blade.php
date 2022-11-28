<div class="row text-center">
    <img src="{{ $user->avatar }}?aspect_ratio=1:1&width=150" alt="" class="img-fluid">
</div>

<div class="row">
    <span class="my-2">{{ "@" . $user->username }}</span>
</div>

<div class="row card card-default list-group">
    <a href="#" class="py-1 px-2 list-group-item list-group-item-action disabled"><i class="fas fa-circle mr-1 text-blue"></i>Chat As Support</a>
    <a href="{{ route('core.admin.user.login_as_user', ['user' => $user->_id]) }}" target="_blank" class="py-1 px-2 list-group-item list-group-item-action">
        <i class="fas fa-circle mr-1 text-blue"></i>Log-In As User
    </a>
    <a href="#" class="py-1 px-2 list-group-item list-group-item-action disabled"><i class="fas fa-circle mr-1 text-blue"></i>Set Password</a>
    <!--<a href="#" class="py-1 px-2 list-group-item list-group-item-action disabled"><i class="fas fa-circle mr-1 text-blue"></i>Gallery</a>-->
    <!--<a href="#" class="py-1 px-2 list-group-item list-group-item-action disabled"><i class="fas fa-circle mr-1 text-blue"></i>Adjustments</a>-->
    <a href="{{ route('chat.admin.chat.index', ['userId' => $user->_id]) }}" class="py-1 px-2 list-group-item list-group-item-action">
        <i class="fas fa-circle mr-1 text-blue"></i>View Chats (n)
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
    @if($user->status == \Aparlay\Core\Models\Enums\UserStatus::SUSPENDED->value)
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
    <a href="#" class="py-1 px-2 list-group-item list-group-item-action disabled"><i class="fas fa-circle mr-1 text-blue"></i>Ban Auto Payouts</a>
    <a href="#" class="py-1 px-2 list-group-item list-group-item-action disabled"><i class="fas fa-circle mr-1 text-blue"></i>Ban Payouts</a>
    <!--<a href="#" class="py-1 px-2 list-group-item list-group-item-action disabled"><i class="fas fa-circle mr-1 text-blue"></i>Enable 14 Day Payout Freeze</a>-->
    <!--<a href="#" class="py-1 px-2 list-group-item list-group-item-action disabled"><i class="fas fa-circle mr-1 text-blue"></i>Region Blacklist</a>-->
    <a href="#" class="py-1 px-2 list-group-item list-group-item-action disabled"><i class="fas fa-circle mr-1 text-blue"></i>Make Invisible</a>
    <!--<a href="#" class="py-1 px-2 list-group-item list-group-item-action disabled"><i class="fas fa-circle mr-1 text-blue"></i>Allow Screenshots</a>-->
    <a href="#" class="py-1 px-2 list-group-item list-group-item-action text-red disabled"><i class="fas fa-circle mr-1 text-red"></i>Delete Account</a>
    <a href="#" class="py-1 px-2 list-group-item list-group-item-action text-red disabled"><i class="fas fa-circle mr-1 text-red"></i>Delete Account w/ Timer</a>
    @if($user->status == \Aparlay\Core\Models\Enums\UserStatus::BLOCKED->value)
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

<livewire:direct-chat :userId="$user->_id"
    :username="$user->username"
    :adminUserId="auth()->user()->_id"
    :adminUsername="auth()->user()->username"
    :category="\Aparlay\Chat\Models\Enums\ChatCategory::SUPPORT->value"
    :headerText="'Support Chat'"/>
