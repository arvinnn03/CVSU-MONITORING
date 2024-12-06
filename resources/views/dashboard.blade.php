@extends('layout')

@section('content')

<ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">Dashboard</li>
        </ol>

        <div class="row">
        </div>

<div class="row">
    <!-- Card 1 -->
    <div class="col-lg-6 mb-4">
        <div class="card bg-primary text-white h-100">
            <div class="card-body">
                <h5 class="card-title">Total Visitors In</h5>
                <p class="card-text">{{ $totalTodayVisits }}</p>
            </div>
        </div>
    </div>
    
    <!-- Card 2 -->
    <div class="col-lg-6 mb-4">
        <div class="card bg-warning text-white h-100">
            <div class="card-body">
                <h5 class="card-title">Total Last 7 Days Visitors</h5>
                <p class="card-text">{{ $totalLast7DaysVisits }}</p>
            </div>
        </div>
    </div>

    <div class="row mt-4">
    <div class="col-lg-6">
        <div class="card bg-success text-light">
            <div class="card-body">
                <h5 class="card-title">Students In</h5>
                <p class="card-text">{{ $totalStudentsIn }}</p>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card bg-danger text-light">
            <div class="card-body">
                <h5 class="card-title">Students Out</h5>
                <p class="card-text">{{ $totalStudentsOut }}</p>
            </div>
        </div>
    </div>
</div>

@endsection
