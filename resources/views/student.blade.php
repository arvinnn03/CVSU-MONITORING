@extends('layout')

@section('content')

<!-- CSS and Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="{{ asset('css/custom.css') }}">
<link rel="stylesheet" href="{{ asset('css/modal.css') }}">

<!-- JS Libraries -->
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.3.1/dist/jsQR.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

<!-- Page Heading -->
<h2 class="mt-3">Student Management</h2>

<!-- Breadcrumbs -->
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Student Management</li>
    </ol>
</nav>

<!-- Main Content -->
<div class="mt-4 mb-4">
    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    <div class="row">
        <!-- Left Column: QR Scanner -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0 rounded-lg mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-qr-code-scan me-2"></i>Student QR Scanner</h5>
                </div>
                <div class="card-body d-flex flex-column justify-content-center">
                    <div class="text-center mb-3">
                        <video id="preview" class="img-fluid rounded"></video>
                        <canvas id="qr-canvas" hidden></canvas>
                    </div>
                    <div id="feedback" class="mt-3 text-center"></div>
                </div>
            </div>
        </div>

        <!-- Right Column: Student Table and Manual Entry -->
        <div class="col-md-8 mb-4">
            <div class="card shadow-sm border-0 rounded-lg mb-4">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-table me-2"></i>Student List</h5>
                    <a href="{{ route('student.add') }}" class="btn btn-light btn-sm rounded-pill">
                        <i class="bi bi-plus-circle me-1"></i>Add New Student
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm" id="student_table">
                            <thead>
                                <tr>
                                    <th>Student ID</th>
                                    <th>Department</th>
                                    <th>Course</th>
                                    <th>In Time</th>
                                    <th>Out Time</th>
                                    <th>Status</th>
                                    <th style="width: 120px;">Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-lg">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-keyboard me-2"></i>Manual Student ID Entry</h5>
                </div>
                <div class="card-body">
                    <div class="input-group">
                        <input type="text" id="student_id_scan" class="form-control" placeholder="Enter Student ID" inputmode="numeric" pattern="[0-9]*" maxlength="10">
                        <button class="btn btn-success" type="button" id="verifyButton">
                            <i class="bi bi-check-circle me-1"></i>Verify
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusUpdateModal" tabindex="-1" aria-labelledby="statusUpdateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-success-custom">
            <div class="modal-body text-center">
                <i class="bi bi-check-circle-fill text-white" style="font-size: 48px;"></i>
                <h4 id="statusMessage" class="mt-2 text-white"></h4>
            </div>
        </div>
    </div>
</div>

<!-- Include the QR scanner library -->
<script src="https://cdn.rawgit.com/sitepoint-editors/jsqrcode/master/src/qr_packed.js"></script>

<!-- Include Bootstrap and DataTables JS -->
<script src="{{ asset('js/bootstrap.js') }}"></script>
<script src="{{ asset('js/dataTables.bootstrap5.min.js') }}"></script>

<!-- Include your custom scripts -->
<script src="{{ asset('js/qr-scanner.js') }}"></script>
<script src="{{ asset('js/student-management.js') }}"></script>

<script src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.min.js"></script>

<style>
    .bg-success-custom {
        background-color: #30a444 !important;
    }
    #statusUpdateModal .modal-content {
        border: none;
    }
    #statusUpdateModal .modal-body {
        padding: 2rem;
    }
    #statusMessage {
        font-weight: bold;
    }
    #preview {
        width: 100%;
        max-width: 100%;
        height: auto;
        transform: scaleX(-1);
        -webkit-transform: scaleX(-1);
    }
    #student_table {
        font-size: 0.9rem;
    }
    #student_table th, #student_table td {
        padding: 0.5rem;
        vertical-align: middle;
    }

    /* Updated button styles */
    .btn-group .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        border-radius: 0.2rem;
    }

    .btn-group .btn i {
        margin-right: 0;
    }

    .btn-group .btn:not(:last-child) {
        margin-right: 0.25rem;
    }

    /* Simplify the Add New Student button */
    .btn-light.rounded-pill {
        background-color: #ffffff;
        color: #28a745;
        border: 1px solid #28a745;
        transition: all 0.3s ease;
        padding: 0.25rem 0.75rem;
        font-size: 0.875rem;
    }

    .btn-light.rounded-pill:hover {
        background-color: #28a745;
        color: #ffffff;
    }

    .btn-light.rounded-pill .bi {
        font-size: 0.9rem;
        margin-right: 0.25rem;
    }

    #qrCodeModal .modal-body img {
        max-width: 100%;
        height: auto;
    }

    /* Add these new styles */
    .btn-group .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }

    .btn-group .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }

    .btn-group .btn-info {
        background-color: #17a2b8;
        border-color: #17a2b8;
    }

    .btn-group .btn-warning {
        background-color: #ffc107;
        border-color: #ffc107;
    }

    .btn-group .btn-danger {
        background-color: #dc3545;
        border-color: #dc3545;
    }
</style>

@endsection
