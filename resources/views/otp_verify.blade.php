<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification Form</title>
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('css/otp-style.css') }}">
    
    <!-- Inline Styles -->
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7f6;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 400px;
            margin-top: 50px;
        }
        .header-logo {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }
        .header-logo img {
            max-width: 100px;
            height: auto;
        }
        .input-field {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }
        .input-field input {
            width: 50px;
            height: 50px;
            text-align: center;
            font-size: 24px;
            margin: 0 5px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            display: block;
            width: 100%;
            padding: 10px;
            font-size: 16px;
            color: #fff;
            background-color: #007bff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .alert-container {
            margin-top: 20px;
        }
        .alert {
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header Section -->
        <header class="text-center mb-4">
            <div class="header-logo">
                <img src="{{ asset('images/cvsu.png') }}" alt="CvSU Logo">
            </div>
            <h4 class="text-center mb-4">Enter OTP Code</h4>
        </header>
        
        <!-- OTP Verification Form -->
        <form id="otp-verify-form" action="{{ route('verifyOtp') }}" method="POST">
            @csrf
            <input type="hidden" name="guard_email" value="{{ $guards }}">
            <input type="hidden" id="combined_otp" name="otp">

            <div class="input-field">
                <input type="text" id="otp_1" maxlength="1" required autofocus>
                <input type="text" id="otp_2" maxlength="1" required>
                <input type="text" id="otp_3" maxlength="1" required>
                <input type="text" id="otp_4" maxlength="1" required>
                <input type="text" id="otp_5" maxlength="1" required>
                <input type="text" id="otp_6" maxlength="1" required>
            </div>
            
            <button type="submit">Verify OTP</button>
        </form>

        <!-- Alert Messages -->
        <div class="alert-container">
            @if(session('message'))
                <div class="alert alert-success">
                    {{ session('message') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const inputs = document.querySelectorAll('.input-field input');

            inputs.forEach((input, index) => {
                input.addEventListener('input', function () {
                    if (this.value.length === this.maxLength && index < inputs.length - 1) {
                        inputs[index + 1].focus();
                    }
                });

                input.addEventListener('keydown', function (e) {
                    if (e.key === 'Backspace' && this.value === '' && index > 0) {
                        inputs[index - 1].focus();
                        inputs[index - 1].value = '';
                    }
                });
            });

            document.getElementById('otp-verify-form').addEventListener('submit', function () {
                const otp = Array.from(inputs).map(input => input.value).join('');
                document.getElementById('combined_otp').value = otp;
            });
        });
    </script>
</body>

</html>
