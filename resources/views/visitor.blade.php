@extends('layout')

@section('content')

<!-- Load CSS -->
<link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
<link rel="stylesheet" href="{{ asset('css/dataTables.bootstrap5.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/visitor-table.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/layout.css') }}">

<!-- Load JS -->
<script src="{{ asset('js/jquery.js') }}"></script>
<script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.js') }}"></script>
<script src="{{ asset('js/dataTables.bootstrap5.min.js') }}"></script>
<meta name="user-id" content="{{ auth()->id() }}"> <!-- Add this line -->

<nav aria-label="breadcrumb">
    <ol class="breadcrumb mt-3">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item active">Visitor Management</li>
    </ol>
</nav>

<div class="mt-4 mb-4">
    <div class="card shadow-sm border-0 rounded-lg">
        <div class="card-header d-flex justify-content-between align-items-center bg-success text-white rounded-top p-3">
            <h5 class="mb-0 font-weight-bold">Visitor Management</h5>
            <a href="visitor/add" class="btn btn-light btn-sm rounded-pill">
                <i class="bi bi-plus-circle"></i> Add Visitor
            </a>
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
                            <th>Gate Out</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Image View Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Visitor Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" class="img-fluid" alt="Visitor Image">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        var table = $('#visitor_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('visitor.fetchall') }}", // Ensure this route is correct
                type: 'GET',
                error: function(xhr, error, thrown) {
                    console.error('Error fetching data:', error);
                    alert('Failed to fetch data. Please try again.');
                }
            },
            columns: [
                { data: 'visitor_image', name: 'visitor_image' }, // Ensure this matches your data
                { data: 'visitor_name', name: 'visitor_name' },
                { data: 'visitor_meet_person_name', name: 'visitor_meet_person_name' },
                { data: 'visitor_department', name: 'visitor_department' },
                { data: 'visitor_enter_time', name: 'visitor_enter_time' },
                { data: 'visitor_out_time', name: 'visitor_out_time' },
                { data: 'visitor_status', name: 'visitor_status' },
                { data: 'name', name: 'name' }, // Ensure this matches your data
                { data: 'visitor_enter_out_by', name: 'visitor_enter_out_by' },
                { data: 'action', name: 'action', orderable: false } // Ensure this is the last column for actions
            ],
            responsive: true,
            deferRender: true,
            dom: 'Bfrtip',
            buttons: [
                { extend: 'copyHtml5', text: '<i class="fas fa-copy"></i>', titleAttr: 'Copy' },
                { extend: 'excelHtml5', text: '<i class="fas fa-file-excel"></i>', titleAttr: 'Export to Excel' },
                { extend: 'pdfHtml5', text: '<i class="fas fa-file-pdf"></i>', titleAttr: 'Export to PDF' },
                { extend: 'print', text: '<i class="fas fa-print"></i>', titleAttr: 'Print' }
            ],
            lengthChange: true,
            pageLength: 10,
            language: {
                search: "Search records:",
                paginate: {
                    next: "Next",
                    previous: "Previous"
                }
            }
        });

        // Handle delete action with confirmation
        $(document).on('click', '.delete', function() {
            var id = $(this).data('id');
            if (confirm("Are you sure you want to remove this visitor?")) {
                $.ajax({
                    url: '/visitor/delete/' + id,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('Visitor deleted successfully.');
                            table.ajax.reload();
                        } else {
                            alert('Error deleting visitor: ' + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('Error deleting visitor. Please try again.');
                        console.error(error);
                    }
                });
            }
        });

        // Handle view image action
        $(document).on('click', '.view-image', function() {
            var imageUrl = $(this).data('image');
            $('#modalImage').attr('src', imageUrl);
            $('#imageModal').modal('show');
        });
    });
</script>

@endsection