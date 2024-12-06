<div class="row">
    <div class="col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Today's Visits</h5>
                <p class="card-text">{{ $totalTodayVisits }}</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Total Visitors Till Today</h5>
                <p class="card-text">{{ $totalVisitorsTillToday }}</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Yesterday's Visits</h5>
                <p class="card-text">{{ $totalYesterdayVisits }}</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Last 7 Days Visits</h5>
                <p class="card-text">{{ $totalLast7DaysVisits }}</p>
            </div>
        </div>
    </div>
</div>
