<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor Management System - Gate 1</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-id" content="{{ auth()->id() }}"> <!-- Add this line -->


    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/modal.css') }}">

    <style>
        #preview {
            height: 400px; /* Adjust this value to match your desired height */
            object-fit: cover;
        }

        .form-container {
            height: 400px; /* Same height as the preview */
            overflow-y: auto;
        }

        .form-container form {
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .form-container .row {
            flex-grow: 1;
            overflow-y: auto;
        }

        .form-container button[type="submit"] {
            margin-top: auto;
        }

        .form-container .form-control {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }

        .form-container label {
            font-size: 0.875rem;
            margin-bottom: 0.25rem;
        }

        .card-header {
            text-align: center;
        }

        .form-label {
            font-weight: bold;
            margin-bottom: 0.25rem;
        }

        .form-control, .form-select {
            padding: 0.375rem 0.75rem;
        }

        .btn-primary {
            background-color: #28a745;
            border-color: #28a745;
        }

        .btn-primary:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }

        .card-header {
            background-color: #28a745;
        }

        #student_table {
            font-size: 0.85rem; /* Smaller font size */
        }

        #visitor_table {
            font-size: 0.85rem; /* Smaller font size */
        }

  .card {
        height: 100%; /* Ensure cards take full height */
    }

    .table-responsive {
        max-height: 300px; /* Limit the height of the table */
        overflow-y: auto; /* Allow vertical scrolling */
    }

    .table-responsive {
        max-height: 300px; /* Limit the height of the table */
        overflow-y: auto; /* Allow vertical scrolling */
    }
    </style>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.3.1/dist/jsQR.min.js"></script>
    <script src="{{ asset('js/qr-scanner.js') }}"></script>
    <!-- <script src="{{ asset('js/student-qr-scanner.js') }}"></script> -->
    <script src="{{ asset('js/visitor-management.js') }}"></script>
</head>
<body>
    @guest
        @yield('content')
    @else
        <!-- Header -->
        <header class="navbar navbar-dark sticky-top green-bg flex-md-nowrap p-0 shadow">
            <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3 d-flex align-items-center" href="/dashboard">
                <!-- Logo -->
                <img src="{{ asset('images/cvsu.png') }}" alt="CvSU Logo" class="logo">
                <span class="ms-2 text-white">CvSU Main</span>
            </a>

            <div class="navbar-nav ms-auto">    
                <div class="nav-item text-nowrap">
                    <a class="nav-link px-3 text-white" href="{{ route('profile') }}">Welcome, {{ Auth::user()->email }}</a>
                </div>
            </div>
            <button class="navbar-toggler d-md-none" type="button" id="sidebarToggle">
                <i class="fa fa-bars"></i>
            </button>

            <a class="nav-link" href="/gate1_visitor/verify">GATE 1</a>

            <a class="nav-link" href="{{ route('logout') }}">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </header>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <!-- Heading and Logo -->
            <div class="d-flex align-items-center justify-content-center pt-3 pb-2 mb-3 border-bottom">
                <!-- Logo in Main Content -->
                <img src="{{ asset('images/cvsu.png') }}" alt="CvSU Logo" class="logo-main me-2">
                <h1 class="h2">Cavite State University Main</h1>
            </div>
            
            @yield('content')
        </main>

        <!-- Camera and Form Container -->
        <div class="container mt-4">
            <div class="row">
                <!-- Camera Preview -->
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 rounded-lg h-100">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">
                            <i class="bi bi-camera-video me-2"></i>
                                Student and Visitor Scanner
                            </h5>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <!-- Camera preview -->
                            <video id="preview" class="img-fluid flex-grow-1"></video>
                            <canvas id="qr-canvas" style="display: none;"></canvas>
                            <!-- Instructions -->
                            <p class="small mt-3">Point your camera at a QR code to scan it. The scan will stop automatically after a successful scan.</p>
                        </div>
                    </div>
                </div>
                <!-- Form -->
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 rounded-lg h-100">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">
                            <i class="bi bi-person-check me-2"></i> 
                                Visitor Information
                            </h5>
                        </div>
                        <div class="card-body form-container">
                            <form id="addVisitorForm" method="POST" action="{{ route('visitor.add_visitor_validation') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="row g-3">
                                    <!-- Name and Email -->
                                    <div class="col-md-6">
                                        <label for="visitor_name" class="form-label">Name</label>
                                        <input type="text" name="visitor_name" id="visitor_name" class="form-control" placeholder="Visitor Name" />
                                        @error('visitor_name')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="visitor_email" class="form-label">Email</label>
                                        <input type="email" name="visitor_email" id="visitor_email" class="form-control" placeholder="Email" />
                                        @error('visitor_email')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Phone and Department -->
                                    <div class="col-md-6">
                                        <label for="visitor_mobile_no" class="form-label">Phone</label>
                                        <input type="tel" name="visitor_mobile_no" id="visitor_mobile_no" class="form-control" 
                                               placeholder="Phone Number" maxlength="11" pattern="\d{11}" 
                                               title="Please enter a valid 11-digit phone number" />
                                        @error('visitor_mobile_no')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="visitor_department" class="form-label">Department</label>
                                        <select name="visitor_department" id="visitor_department" class="form-select" data-departments="{{ json_encode($data->keyBy('department_name')->toArray()) }}">
                                            <option value="">Select</option>
                                            @foreach($data as $department)
                                                <option value="{{ $department->department_name }}">{{ $department->department_name }}</option>
                                            @endforeach
                                        </select>
                                        @error('visitor_department')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Image and Status -->
                                    <div class="col-md-6">
                                        <label for="visitor_image" class="form-label">Image</label>
                                        <input type="file" name="visitor_image" id="visitor_image" class="form-control" accept="image/*">
                                    </div>

                                    <div class="col-md-6">
                                        <label for="visitor_status" class="form-label">Status</label>
                                        <select name="visitor_status" id="visitor_status" class="form-select">
                                            <option value="-" disabled selected>-</option>
                                            <option value="In">In</option>
                                            <option value="Out">Out</option>
                                        </select>
                                        @error('visitor_status')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Person To Visit and Purpose -->
                                    <div class="col-md-6">
                                        <label for="visitor_meet_person_name" class="form-label">Person To Visit</label>
                                        <input type="text" name="visitor_meet_person_name" id="visitor_meet_person_name" class="form-control" placeholder="Person To Visit" />
                                        @error('visitor_meet_person_name')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="visitor_reason_to_meet" class="form-label">Purpose</label>
                                        <input type="text" name="visitor_reason_to_meet" id="visitor_reason_to_meet" class="form-control" placeholder="Purpose" />
                                        @error('visitor_reason_to_meet')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Time In and Exit Time -->
                                    <div class="col-md-6">
                                        <label for="visitor_enter_time" class="form-label">Time In</label>
                                        <input type="text" name="visitor_enter_time" id="visitor_enter_time" class="form-control" readonly />
                                        @error('visitor_enter_time')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="visitor_out_time" class="form-label">Exit Time</label>
                                        <input type="text" name="visitor_out_time" id="visitor_out_time" class="form-control" readonly />
                                        @error('visitor_out_time')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <input type="hidden" name="visitor_enter_by" value="{{ auth()->id() }}" />
                                <input type="hidden" name="visitor_enter_out_by" value="{{ auth()->id() }}" />
                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary">Add Visitor</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

     <!-- Visitor Management Table and Student List -->
<div class="container mt-4 mb-4">
    <div class="row">
        <!-- Visitor Management Table -->
        <div class="col-md-12"> <!-- Changed to col-md-12 for full width -->
            <div class="card shadow-sm border-0 rounded-lg mx-auto h-100">
                <div class="card-header d-flex justify-content-center align-items-center bg-success text-white rounded-top p-3">
                    <h5 class="mb-0 font-weight-bold">
                    <i class="bi bi-table me-2"></i>
                        Visitor Management Table
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="visitor_table">
                            <thead>
                                <tr>
                                    <th>Visitor ID</th>
                                    <th>Visitor Name</th>
                                    <th>Person Need to Visit</th>
                                    <th>Office/Department</th>
                                    <th>In Time</th>
                                    <th>Out Time</th>
                                    <th>Status</th>
                                    <th>Gate In</th>
                                    <th>Gate Out</th> <!-- Ensure this header is present -->
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Student List -->
<div class="container mt-4 mb-4">
    <div class="row">
        <div class="col-md-12"> <!-- Changed to col-md-12 for full width -->
            <div class="card shadow-sm border-0 rounded-lg mx-auto h-100">
                <div class="card-header d-flex justify-content-center align-items-center bg-success text-white rounded-top p-3">
                    <h5 class="mb-0 font-weight-bold">
                    <i class="bi bi-table me-2"></i>
                        Student Management Table
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="student_table">
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
        </div>
    </div>
</div>

    <!-- Image View Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">Visitor Image</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" src="{{ asset('storage/app/public/images') }}" class="img-fluid" alt="Visitor Image">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

 <!-- Status Update Modal -->
 <div class="modal fade" id="statusUpdateModal" tabindex="-1" aria-labelledby="statusUpdateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-transparent border-0">
                <div class="modal-body text-center">
                    <i class="bi bi-check-circle text-success" style="font-size: 150px;"></i>
                    <h2 id="statusMessage" class="mt-3 text-white"></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/jquery.js') }}"></script>
    <script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.js') }}"></script>
    <script src="{{ asset('js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('js/student-management.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('#user_table').DataTable();
        });
    </script>
    @endguest
</body>
</html>