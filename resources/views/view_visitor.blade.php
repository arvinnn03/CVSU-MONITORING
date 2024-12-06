@extends('layout')

@section('content')

<nav aria-label="breadcrumb">
    <ol class="breadcrumb mt-3">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('visitor.index') }}">Visitor Management</a></li>
        <li class="breadcrumb-item active" aria-current="page">Visitor Details</li>
    </ol>
</nav>

<div class="container mt-4">
    <div class="card shadow-sm border-light" style="max-width: 600px; margin: auto;">
        <div class="card-header d-flex justify-content-between align-items-center bg-warning text-dark position-relative">
            <span class="h5 mb-0">
                <i class="fas fa-user-check"></i> Visitor Details
            </span>
            @if(Auth::user()->type == 'Admin')
                <a href="{{ route('visitor.index') }}" class="btn btn-success btn-sm rounded-pill">Back</a>
            @elseif(Auth::user()->type == 'User1')
                <a href="{{ route('gate1_visitor.addverify') }}" class="btn btn-success btn-sm rounded-pill">Back</a>
            @elseif(Auth::user()->type == 'User2')
                <a href="{{ route('gate2_visitor.addverify') }}" class="btn btn-success btn-sm rounded-pill">Back</a>
            @elseif(Auth::user()->type == 'User3')
                <a href="{{ route('gate3_visitor.addverify') }}" class="btn btn-success btn-sm rounded-pill">Back</a>
            @endif
        </div>
        
        <div class="card-body">
            <dl class="row">
                <div class="col-6 mb-3">
                    <dt class="font-weight-bold">Visitor Name</dt>
                    <dd class="mb-0">{!! $data->visitor_name !!}</dd>
                </div>
                <div class="col-6 mb-3">
                    <dt class="font-weight-bold">Email</dt>
                    <dd class="mb-0">{!! $data->visitor_email !!}</dd>
                </div>
                <div class="col-6 mb-3">
                    <dt class="font-weight-bold">Person to Visit</dt>
                    <dd class="mb-0">{!! $data->visitor_meet_person_name !!}</dd>
                </div>
                <div class="col-6 mb-3">
                    <dt class="font-weight-bold">Department</dt>
                    <dd class="mb-0">{!! $data->visitor_department !!}</dd>
                </div>
                <div class="col-12 mb-3">
                    <dt class="font-weight-bold">Purpose</dt>
                    <dd class="mb-0">{!! $data->visitor_reason_to_meet !!}</dd>
                </div>
                <div class="col-6 mb-3">
                    <dt class="font-weight-bold">Time In</dt>
                    <dd class="mb-0">{!! $data->visitor_enter_time !!}</dd>
                </div>
                <div class="col-6 mb-3">
                    <dt class="font-weight-bold">Out Time</dt>
                    <dd class="mb-0">{!! $data->visitor_out_time ?? 'N/A' !!}</dd>
                </div>
                <div class="col-12 mb-3">
                    <dt>Encoded By</dt>
                    <dd class="mb-0">{!! optional($data->user)->name ?? 'N/A' !!}</dd>
                </div>
            </dl>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .card {
        border-radius: 8px; /* Rounded corners */
    }
    .card-header {
        border-radius: 8px 8px 0 0; /* Rounded top corners */
        position: relative;
        padding: 1rem;
    }
    .card-body {
        font-size: 1rem; /* Slightly larger font size */
    }
    .card-body dl {
        margin: 0;
    }
    .card-body dt {
        font-weight: bold;
        color: #495057; /* Darker text color */
    }
    .card-body dd {
        margin: 0 0 0.5rem; /* Less margin for dd */
        color: #6c757d; /* Lighter text color */
    }
    .badge-success {
        background-color: #28a745; /* Green for 'In' */
    }
    .badge-danger {
        background-color: #dc3545; /* Red for 'Out' */
    }
    .btn-success {
        background-color: #28a745; /* Green background for the button */
        border-color: #28a745; /* Green border */
    }
    .btn-success:hover {
        background-color: #218838; /* Darker green on hover */
        border-color: #1e7e34; /* Darker green border on hover */
    }
    .h5 i {
        margin-right: 0.5rem; /* Spacing for the icon */
    }
</style>
@endpush
