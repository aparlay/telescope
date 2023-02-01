<div class="row mb-3">
    <div class="col-md-2">
        <div class="card mb-4 mb-xl-0">
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <span class="mb-0 d-block text-center">
                            0
                        </span>
                        <span class="mb-0 d-block align-middle text-center">Profile Views</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card mb-4 mb-xl-0">
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <span class="mb-0 d-block text-center">
                            0
                        </span>
                        <span class="mb-0 d-block align-middle text-center">Likes</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card mb-4 mb-xl-0">
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <span class="mb-0 d-block text-center">
                            0
                        </span>
                        <span class="mb-0 d-block align-middle text-center">Followers</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card mb-4 mb-xl-0">
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <span class="mb-0 d-block text-center">
                            {{ money((int)($user->stats['amounts']['spent']['tips'] ?? 0), 'USD') }}
                        </span>
                        <span class="mb-0 d-block align-middle text-center">Sent Tips</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card mb-4 mb-xl-0">
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <span class="mb-0 d-block text-center">
                            {{ money((int)($user->stats['amounts']['earned']['commissions']['tips'] ?? 0), 'USD') }}
                        </span>
                        <span class="mb-0 d-block align-middle text-center">Received Tips</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card mb-4 mb-xl-0">
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <span class="mb-0 d-block text-center">
                            {{ money((int)($user->stats['amounts']['earned']['referral']['subscriptions'] ?? 0), 'USD') }}
                        </span>
                        <span class="mb-0 d-block align-middle text-center">Payouts ($)</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
