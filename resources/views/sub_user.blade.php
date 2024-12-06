@extends('layout')

@section('content')

<!-- Styles and Scripts -->
<link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="{{ asset('css/sub_user.css') }}">

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

<!-- Page Heading -->
<h2 class="mt-3">Sub User Management</h2>

<!-- Breadcrumbs -->
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Sub User Management Table</li>
    </ol>
</nav>

<!-- Main Content -->
<div class="mt-4 mb-4">
    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    <div class="card shadow-sm border-0 rounded-lg">
        <div class="card-header d-flex justify-content-between align-items-center bg-success text-white rounded-top p-3">
            <h5 class="mb-0 font-weight-bold">Sub User Management</h5>
            <a href="{{ route('sub_user.add') }}" class="btn btn-light btn-sm rounded-pill">
                <i class="bi bi-plus-circle"></i> Add
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="user_table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
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

<!-- DataTables Initialization and AJAX Script --><script>
    $(document).ready(function() {
        // Function to format date and time
        function formatDateTime(dateTime) {
            const options = {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
                hour12: true
            };
            return new Date(dateTime).toLocaleString('en-US', options).replace(',', ''); // Format MM/DD/YYYY HH:MM:SS AM/PM
        }

        // Check if DataTable is already initialized and destroy it
        if ($.fn.DataTable.isDataTable('#user_table')) {
            $('#user_table').DataTable().clear().destroy();
        }

        // Initialize DataTable
        $('#user_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('sub_user.fetchall') }}",
            columns: [
                { data: 'name', name: 'name' },
                { data: 'email', name: 'email' },
                {
                    data: 'created_at',
                    name: 'created_at',
                    render: formatDateTime
                },
                {
                    data: 'updated_at',
                    name: 'updated_at',
                    render: formatDateTime
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ]
        });

        // Handle delete button click
        $(document).on('click', '.delete', function() {
            const id = $(this).data('id');
            if (confirm("Are you sure you want to remove this item?")) {
                window.location.href = `/sub_user/delete/${id}`;
            }
        });
    });
</script>

@endsection
