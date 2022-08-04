<div class="row">
    <div class="col-md-3 mb-3">
        <div class="card card-stats mb-4 mb-xl-0 card-danger card-outline">
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <span class="mb-0 text-danger d-block">
                            <i class="fas fa-chevron-down"></i>
                            <span class="h2 font-weight-bold">
                                {{ money((int)$user->stats['amounts']['spent']['tips'], 'USD') }}
                            </span>
                        </span>
                        <h5 class="card-title text-uppercase text-muted mb-0 d-block">Sent Tips</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card card-stats mb-4 mb-xl-0 card-success card-outline">
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <span class="mb-0 text-success d-block">
                            <i class="fas fa-chevron-up"></i>
                            <span class="h2 font-weight-bold">
                                {{ money((int)$user->stats['amounts']['earned']['commissions']['tips'], 'USD') }}
                            </span>
                        </span>
                        <h5 class="card-title text-uppercase text-muted mb-0 d-block">Rec. Tips</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card card-stats mb-4 mb-xl-0 card-danger card-outline">
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <span class="mb-0 text-danger d-block">
                            <i class="fas fa-chevron-down"></i>
                            <span class="h2 font-weight-bold">
                                {{ money((int)$user->stats['amounts']['spent']['subscriptions'], 'USD') }}
                            </span>
                        </span>
                        <h5 class="card-title text-uppercase text-muted mb-0 d-block">Subscriptions</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card card-stats mb-4 mb-xl-0 card-success card-outline">
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <span class="mb-0 text-success d-block">
                            <i class="fas fa-chevron-up"></i>
                            <span class="h2 font-weight-bold">
                                {{ money((int)$user->stats['amounts']['earned']['commissions']['subscriptions'], 'USD') }}
                            </span>
                        </span>
                        <h5 class="card-title text-uppercase text-muted mb-0 d-block">Subscribers</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card card-stats mb-4 mb-xl-0 card-primary card-outline">
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <span class="d-block text-primary h2 font-weight-bold">
                            {{ money((int)$user->stats['amounts']['earned']['referral']['subscriptions'], 'USD') }}
                        </span>
                        <h5 class="card-title text-uppercase text-muted d-block">Ref. Earning</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card card-stats mb-4 mb-xl-0 card-primary card-outline">
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <span class="d-block text-primary h2 font-weight-bold">
                            {{ money((int)$user->stats['amounts']['earned']['referral']['subscriptions'], 'USD') }}
                        </span>
                        <h5 class="card-title text-uppercase text-muted d-block">Ref. Commission</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card card-stats mb-4 mb-xl-0 card-primary card-outline">
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <span class="d-block text-primary h2 font-weight-bold">
                            {{ money((int)$user->stats['amounts']['earned']['referral']['subscriptions'], 'USD') }}
                        </span>
                        <h5 class="card-title text-uppercase text-muted d-block">Downline Users</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card card-stats mb-4 mb-xl-0 card-primary card-outline">
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <span class="d-block text-primary h2 font-weight-bold">
                            {{ money((int)$user->stats['amounts']['earned']['referral']['subscriptions'], 'USD') }}
                        </span>
                        <h5 class="card-title text-uppercase text-muted d-block">Payouts</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
