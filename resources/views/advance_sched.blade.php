<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor Appointment</title>

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
    <!-- <link rel="stylesheet" href="{{ asset('css/layout.css') }}"> -->
    <link rel="stylesheet" href="{{ asset('css/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css">

    <script src="{{ asset('js/advance-sched.js') }}" defer></script>

    <style>
        /* Background styling */
        body {
            background-image: url('/images/background.jpg');
            background-size: cover; 
            background-position: center; 
            height: 100vh; 
            margin: 0; 
            display: flex; 
            align-items: flex-start; 
            justify-content: center; 
            padding-top: 50px; 
            font-family: 'Arial', sans-serif; 
            position: relative; /* Position relative for the pseudo-element */
        }

        .body-background::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: inherit; /* Inherit the background image */
            filter: blur(200px); /* Adjust blur amount */
            z-index: -1; /* Ensure it stays behind content */
            opacity: 0.7; /* Adjust opacity for the desired effect */
        }

        .container {
            position: relative; /* Ensure container is above the blur */
            z-index: 1; /* Bring container above the background */
        }


        .header {
            background-color: #28a745; 
            color: white; 
            padding: 15px 20px; 
            border-radius: 100px; 
            width:100%; /* Set to 100% */
            max-width: 800px; 
            margin: 0 auto; 
            text-align: center; /* Center text */
        }

        .form-container {
            position: relative; 
            z-index: 1; 
            background-color: rgba(255, 255, 255, 0.9); 
            padding: 30px; 
            border-radius: 15px; 
            margin-top: 20px; /* Adjust if needed */
            max-width: 800px; 
            width: 100%; /* Set to 100% */
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            margin-left: auto; /* Center align */
            margin-right: auto; /* Center align */
        }
        @media (max-width: 576px) {
        .form-container {
            margin-top: 30px; /* Increase for smaller screens if needed */
        }
            }

        .form-label {
            font-weight: bold;
            margin-bottom: 0.5rem;
            text-align: left; /* Left align for better readability */
        }

        .form-control {
            padding: 0.75rem; /* Increased padding for better touch target */
            border-radius: 5px; /* Rounded corners */
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1); /* Subtle inset shadow */
        }

        .btn-primary {
            background-color: #28a745;
            border-color: #28a745;
            border-radius: 5px; /* Rounded corners */
            transition: background-color 0.3s ease; /* Smooth transition */
        }

        .btn-primary:hover {
            background-color: #218838;
            border-color: #1e7e34;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2); /* Shadow on hover */
        }
    </style>
</head><body class="body-background">
    <div class="container">
        <div class="header d-flex align-items-center justify-content-center">
            <img src="/images/cvsu.png" alt="Logo" class="logo me-2" style="height: 50px;">
            <h1>Visitor Appointment</h1>
        </div>
        <div class="form-container">
            <form id="addVisitorForm" method="POST" action="{{ route('advancevisitor.add_visitor_validation') }}" enctype="multipart/form-data" class="p-4">
                @csrf
                <div class="row g-3">
    <div class="col-md-6">
        <label for="visitor_name" class="form-label">Name</label>
        <input type="text" name="visitor_name" id="visitor_name" class="form-control" placeholder="Visitor Name" required />
        @error('visitor_name')
            <span class="text-danger small">{{ $message }}</span>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="visitor_email" class="form-label">Email</label>
        <input type="email" name="visitor_email" id="visitor_email" class="form-control" placeholder="Email" required />
        @error('visitor_email')
            <span class="text-danger small">{{ $message }}</span>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="visitor_mobile_no" class="form-label">Phone</label>
        <input type="tel" name="visitor_mobile_no" id="visitor_mobile_no" class="form-control" 
               placeholder="Phone Number" maxlength="11" pattern="\d{11}" 
               title="Please enter a valid 11-digit phone number" required />
        @error('visitor_mobile_no')
            <span class="text-danger small">{{ $message }}</span>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="visitor_department" class="form-label">Department</label>
        <select name="visitor_department" id="visitor_department" class="form-select" required>
            <option value="">Select</option>
            @foreach($data as $department)
                <option value="{{ $department->department_name }}">{{ $department->department_name }}</option>
            @endforeach
        </select>
        @error('visitor_department')
            <span class="text-danger small">{{ $message }}</span>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="visitor_image" class="form-label">Image</label>
        <input type="file" name="visitor_image" id="visitor_image" class="form-control" accept="image/*">
    </div>

    <div class="col-md-6">
        <label for="visitor_meet_person_name" class="form-label">Person To Visit</label>
        <input type="text" name="visitor_meet_person_name" id="visitor_meet_person_name" class="form-control" placeholder="Person To Visit" required />
        @error('visitor_meet_person_name')
            <span class="text-danger small">{{ $message }}</span>
        @enderror
    </div>

    <!-- Change the "Purpose" field to span the full width -->
    <div class="col-md-12">
        <label for="visitor_reason_to_meet" class="form-label">Purpose</label>
        <input type="text" name="visitor_reason_to_meet" id="visitor_reason_to_meet" class="form-control" placeholder="Purpose" required />
        @error('visitor_reason_to_meet')
            <span class="text-danger small">{{ $message }}</span>
        @enderror
    </div>
</div>
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Add Visitor</button>
                </div>
            </form>
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
                    <img id="modalImage" src="" class="img-fluid" alt="Visitor Image">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
