
<div class="row">
    <div class="col-xl-3 col-lg-6">
        <div class="card card-stats mb-4 mb-xl-0  card-primary card-outline">
        <div class="card-body">
            <div class="row">
            <div class="col">
                
                <span class="h2 font-weight-bold mb-0">{{number_format($user->stats['amounts']['sent_tips'], 2) }} USD</span>
                <h5 class="card-title text-uppercase text-muted mb-0">Total Send Tips</h5>
            </div>
            <div class="col-auto">
                <div class="icon icon-shape bg-danger text-white rounded-circle shadow">
                    <i class="fas fa-usd-circle"></i>
                </div>
            </div>
            </div>
           
        </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6">
        <div class="card card-stats mb-4 mb-xl-0 card-success card-outline">
        <div class="card-body">
            <div class="row">
            <div class="col">
                <span class="h2 font-weight-bold mb-0">{{number_format($user->stats['amounts']['received_tips'], 2) }} USD</span>
                <h5 class="card-title text-uppercase text-muted mb-0">Total Received Tips</h5>
                
            </div>
            <div class="col-auto">
                <div class="icon icon-shape bg-warning text-white rounded-circle shadow">
                    <i class="fas fa-usd-circle"></i>
                </div>
            </div>
            </div>
           
        </div>
        </div>
    </div>
    
   
</div>
       