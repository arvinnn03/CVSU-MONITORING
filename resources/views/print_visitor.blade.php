<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor Details</title>
    
    <!-- Styles -->
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f7f6;
            color: #333;
        }
        .container {
            max-width: 700px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .card {
            border: none;
            border-radius: 8px;
            margin-bottom: 20px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            color: #28a745;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card-header img {
            height: 50px;
            margin-right: 15px;
        }
        .card-body {
            font-size: 16px;
            padding: 15px;
        }
        dl.row {
            display: flex;
            flex-wrap: wrap;
            margin: 0;
        }
        dt, dd {
            margin: 0;
            padding: 10px;
            width: 50%;
            box-sizing: border-box;
        }
        dt {
            font-weight: bold;
            background-color: #f8f9fa;
            border-bottom: 1px solid #ddd;
        }
        dd {
            background-color: #ffffff;
            border-bottom: 1px solid #ddd;
        }
        .badge {
            font-size: 14px;
            padding: 5px 10px;
            border-radius: 10px;
            display: inline-block;
            text-align: center;
        }
        .bg-success {
            background-color: #28a745;
            color: #ffffff;
        }
        .bg-danger {
            background-color: #dc3545;
            color: #ffffff;
        }
        .qr-code {
            text-align: center;
            margin-bottom: 30px;
        }
        canvas {
            max-width: 100%;
            height: auto;
        }
        .btn-back {
            display: inline-block;
            margin-bottom: 20px;
            font-size: 16px;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            color: #ffffff;
            background-color: #0056b3;
            text-decoration: none;
            text-align: center;
        }
        .btn-back:hover {
            background-color: #03ac13;
        }
    </style>
    
    <!-- QR Code Library -->
    <script src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.min.js"></script>
</head>
<body>
    <div class="container">
        <!-- Back Button -->
        @if(Auth::user()->type == 'Admin')
        <a href="/visitor" class="btn-back">Back To Visitor List</a>
        @elseif(Auth::user()->type == 'User1')
        <a href="/gate1_visitor/verify" class="btn-back">Back To Visitor List</a>
        @elseif(Auth::user()->type == 'User2')
        <a href="/gate2_visitor/verify" class="btn-back">Back To Visitor List</a>
        @elseif(Auth::user()->type == 'User3')
        <a href="/gate3_visitor/verify" class="btn-back">Back To Visitor List</a>
        @endif

        <!-- Card for Visitor Details -->
        <div class="card">
            <div class="card-header">
                <img src="{{ asset('images/cvsu.png') }}" alt="Logo">
                Visitor Details
            </div>
            <div class="card-body">
                <!-- QR Code Container -->
                <div class="qr-code">
                    <h5>QR Code</h5>
                    <div id="qrcode"></div>
                </div>

                <!-- Visitor Details -->
                <dl class="row">
                    <dt>Visitor Name:</dt>
                    <dd>{{ $data->visitor_name }}</dd>

                    <dt>Email:</dt>
                    <dd>{{ $data->visitor_email }}</dd>

                    <dt>Mobile No:</dt>
                    <dd>{{ $data->visitor_mobile_no }}</dd>

                    <dt>Person to Visit:</dt>
                    <dd>{{ $data->visitor_meet_person_name }}</dd>

                    <dt>Department:</dt>
                    <dd>{{ $data->visitor_department }}</dd>

                    <dt>Reason to Meet:</dt>
                    <dd>{{ $data->visitor_reason_to_meet }}</dd>

                    <dt>Enter Time:</dt>
                    <dd>{{ $data->visitor_enter_time ?? 'N/A'}}</dd>

                    <dt>Out Time:</dt>
                    <dd>{{ $data->visitor_out_time ?? 'N/A' }}</dd>

                    <dt>Status:</dt>
                    <dd>
                        @if($data->visitor_status === 'In')
                            <span class="badge bg-success">In</span>
                        @else
                            <span class="badge bg-danger">Out</span>
                        @endif
                    </dd>

                    <dt>Encoded by:</dt>
                    <dd>{{ optional($data->user)->name ?? 'N/A' }}</dd>
                </dl>
            </div>
        </div>
    </div>

    <!-- QR Code Generation Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var qr = qrcode(0, 'M');
            qr.addData(JSON.stringify({ token: '{{ $data->unique_token }}' }));
            qr.make();
            document.getElementById('qrcode').innerHTML = qr.createImgTag(5, 10);
        });
    </script>
</body>
</html>
