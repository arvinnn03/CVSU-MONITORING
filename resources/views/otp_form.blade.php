@extends('layout')

@section('content')

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/otp_form.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css">
</head>
<body>

<div class="container mt-5">
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-success text-white text-center">
            <h2 class="mb-0">Guard Information Sheet</h2>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form id="add-email-form" action="{{ route('store') }}" method="POST">
                @csrf
                <div class="form-group mb-3">
                    <label for="guard_name" class="form-label">Name:</label>
                    <input type="text" id="guard_name" name="guard_name" class="form-control" placeholder="Enter guard's name" required>
                </div>
                <div class="form-group mb-3">
                    <label for="guard_email" class="form-label">Email:</label>
                    <input type="email" id="guard_email" name="guard_email" class="form-control" placeholder="Enter guard's email" required>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-success btn-block">Add Email</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-lg mt-4">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center rounded-top p-3">
            <span class="font-weight-bold">Guards</span>
            <button id="sendOtpBtn" class="btn btn-light btn-sm rounded-pill">
                Send OTP
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped text-center" id="otp_table">
                    <thead>
                        <tr>
                            <th>Guard Name</th>
                            <th>Guard Email</th>
                            <th>Status</th>
                            <th>OTP</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($guards as $Otp)
                            <tr>
                                <td>{{ $Otp->guard_name }}</td>
                                <td>{{ $Otp->guard_email }}</td>
                                <td>
                                    <select class="form-control status-select" data-id="{{ $Otp->id }}">
                                        <option value="On Duty" {{ $Otp->guard_status == 'On Duty' ? 'selected' : '' }}>On Duty</option>
                                        <option value="Off Duty" {{ $Otp->guard_status == 'Off Duty' ? 'selected' : '' }}>Off Duty</option>
                                    </select>
                                </td>
                                <td>{{ $Otp->otp ?? 'N/A' }}</td>
                                <td class="table-actions">
                                    <button type="button" class="btn btn-resend" data-email="{{ $Otp->guard_email }}">
                                        <i class="bi bi-arrow-clockwise"></i> Resend OTP
                                    </button>
                                    <button type="button" class="btn btn-delete" data-id="{{ $Otp->id }}" data-status="{{ $Otp->guard_status }}">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Include jQuery for AJAX -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('.btn-resend').on('click', function() {
            const email = $(this).data('email');

            $.ajax({
                url: '{{ route("resendOtp") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    guard_email: email
                },
                success: function(response) {
                    alert(response.message);
                },
                error: function(xhr) {
                    alert(xhr.responseJSON.message);
                }
            });
        });

        $(document).on('click', '.btn-delete', function() {
            var id = $(this).data('id');
            var status = $(this).data('status');

            if (status === 'On Duty') {
                alert("Cannot delete a guard who is on duty.");
                return;
            }

            if (confirm("Are you sure you want to delete this guard? This action cannot be undone.")) {
                $.ajax({
                    url: '{{ route("otp.delete", "") }}/' + id,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        _method: 'DELETE'
                    },
                    success: function(result) {
                        alert('Guard deleted successfully.');
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        alert('Error deleting guard: ' + xhr.responseJSON.message || error);
                    }
                });
            }
        });

        $(document).on('change', '.status-select', function() {
            const id = $(this).data('id');
            const status = $(this).val();

            $.ajax({
                url: '{{ route("updateStatus") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    status: status
                },
                success: function(response) {
                    alert(response.message);
                },
                error: function(xhr) {
                    alert('Error updating status: ' + xhr.responseText);
                }
            });
        });

        // Add this new event listener for the Send OTP button
        $('#sendOtpBtn').on('click', function(e) {
            e.preventDefault();
            
            $.ajax({
                url: '{{ route("generateAndSendOtp") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    alert(response.message);
                    location.reload(); // Reload the page to show updated OTPs
                },
                error: function(xhr) {
                    alert('Error sending OTP: ' + xhr.responseText);
                }
            });
        });
    });
</script>

@endsection
