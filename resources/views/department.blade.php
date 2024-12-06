@extends('layout')

@section('content')

<!-- Include Bootstrap CSS -->
<link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">

<!-- Include Custom CSS -->
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="{{ asset('css/sub_user.css') }}">

<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Include DataTables JS -->
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

<h2 class="mt-3">Department & Office Management</h2>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item active">Department & Office Management</li>  
    </ol>
</nav>

<div class="container mt-4 mb-4">
    @if(session()->has('success'))
        <div class="alert alert-success">
            {{ session()->get('success') }}
        </div>
    @endif

    <div class="mt-4 mb-4">
    <div class="card shadow-sm border-0 rounded-lg">
        <div class="card-header d-flex justify-content-between align-items-center bg-success text-white rounded-top p-3">
            <h5 class="mb-0 font-weight-bold">Department & Office Management</h5>
            <a href="/department/add" class="btn btn-light btn-sm rounded-pill">
    <i class="bi bi-plus-circle"></i> Add
</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="department_table">
                    <thead>
                        <tr>
                            <th>Department/Office Name</th>
                            <th>Course</th>
                            <th>Created At</th>
                            <th>Updated At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    function formatDateTime(dateTime) {
        const options = {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
            hour12: true
        };
        const date = new Date(dateTime);
        return date.toLocaleString('en-US', options).replace(',', ''); // Format MM/DD/YYYY HH:MM:SS AM/PM
    }

    $('#department_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("department.fetch_all") }}',
            error: function(xhr, error, thrown) {
                console.error('AJAX Error: ', thrown);
            }
        },
        columns: [
            { data: 'department_name', name: 'department_name' },
            { data: 'course_name', name: 'course_name' },
            { data: 'created_at', name: 'created_at', render: formatDateTime },
            { data: 'updated_at', name: 'updated_at', render: formatDateTime },
            { data: 'action', name: 'action', orderable: false }
        ]
    });

    $(document).on('click', '.delete', function() {
        var id = $(this).data('id');
        if (confirm("Are you sure you want to remove it?")) {
            window.location.href = '/department/delete/' + id;
        }
    });
});
</script>

@endsection
