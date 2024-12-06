@extends('layout')
@section('content')

<style>
    /* Background styling */
.login-background {
    background-image: url('/images/background.jpg'); 
    background-size: cover; 
    background-position: center; 
    background-repeat: no-repeat; /* Prevents the image from repeating */
    height: 100vh; /* Full viewport height */
    width: 100vw; /* Full viewport width */
    position: fixed; /* Fixed position to stay in place */
    top: 0; /* Align to the top */
    left: 0; /* Align to the left */
    display: flex; /* Centers content vertically */
    align-items: center; /* Centers content vertically */
    justify-content: center; /* Centers content horizontally */
    z-index: -1; /* Ensures the background is behind other content */
}

.login-background::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: inherit; /* Inherit the background image */
    filter: blur(200px); /* Adjust blur amount */
    z-index: -1; /* Ensure it stays behind content */
    opacity: 0.6; /* Adjust opacity for the desired effect */
}

.login-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-start; /* Aligns items to the top of the container */
    width: 100%;
    height: 100%; /* Ensures full viewport height */
    position: relative;
    padding-top: 50px; /* Moves the container down from the top */
}

.gif-container {
    margin-bottom: 20px; /* Space between the GIF and the login form */
    margin-left: 50px;
    max-width: 100%; /* Ensures it doesn’t overflow */
    text-align: center;
}

.gif-container img {
    max-width: 100%; /* Makes the GIF responsive */
    height: auto; /* Maintains aspect ratio */
}

.login-form {
    padding: 30px; /* Adds padding */
    background-color: rgba(255, 255, 255, 0.85); /* Slightly more opaque background for better readability */
    border-radius: 10px; /* Rounds the corners */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3); /* Adds a subtle shadow for depth */
    max-width: 400px; /* Sets a maximum width */
    width: 100%; /* Ensures it takes up full width within its container */
}

.card-header {
    background-color: #205c04; /* Green color for header */
    color: white; /* White text for contrast */
    border-bottom: 1px solid #d4edda; /* Light green border */
}

.btn-success {
    background-color: #205c04; /* Green color for the button */
    border: none; /* Removes border */
    border-radius: 5px; /* Rounds corners of the button */
    font-weight: bold; /* Makes button text bold */
}

.btn-success:hover {
    background-color: #1a4b2e; /* Darker green for hover effect */
}

</style>

<main class="login-background">
    <div class="login-container">
        <!-- GIF container -->
        <div class="gif-container">
            <img src="{{ url('images/loginn.png') }}" alt="Background GIF">
        </div>
        <div class="login-form">
            <div class="card">
                <h3 class="card-header text-center">LOG IN YOUR ACCOUNT</h3>
                <div class="card-body">
                    @if (session()->has('error'))
                    <div class="alert alert-danger">
                        {{ session()->get('error') }}
                    </div>
                    @endif

                    <form method="post" action="{{ route('login.custom') }}">
                        @csrf

                        <div class="form-group mb-3">
                            <input type="text" name="email" class="form-control" placeholder="Email" />
                            @if ($errors->has('email'))
                            <span class="text-danger">{{ $errors->first('email') }}</span>
                            @endif
                        </div>

                        <div class="form-group mb-3">
                            <input type="password" name="password" class="form-control" placeholder="Password" />
                            @if ($errors->has('password'))
                            <span class="text-danger">{{ $errors->first('password') }}</span>
                            @endif
                        </div>

                        <div class="d-grid mx-auto">
                            <button type="submit" class="btn btn-success btn-block">Login</button>
                        </div>
                    </form>
                    
                    <!-- Visitor Appointment link -->
                    <div class="mt-3 text-center">
                        <p>Visitor Appointment? <a href="{{ route('advance_sched') }}" class="text-primary">Click here</a></p>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</main>

@endsection
