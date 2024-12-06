<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f9f9f9;
        }
        .container {
            width: 90%;
            max-width: 600px;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1, h2 {
            text-align: center;
            color: #207c34;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .qr-code {
            text-align: center;
        }
        .qr-code p {
            margin-top: 10px;
            font-size: 14px;
            color: #555;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.min.js"></script>
</head>
<body>
    <div class="container">
        <h1>Student Details</h1>
        <table>
            <tr>
                <th>Student ID</th>
                <td>{{ $student->stud_id }}</td>
            </tr>
            <tr>
                <th>Department</th>
                <td>{{ $student->student_department }}</td>
            </tr>
            <tr>
                <th>Course</th>
                <td>{{ $student->student_course }}</td>
            </tr>
            <tr>
                <th>Last Entry Time</th>
                <td>{{ $student->student_enter_time ? $student->student_enter_time->format('m/d/Y h:i A') : 'N/A' }}</td>
            </tr>
            <tr>
                <th>Last Exit Time</th>
                <td>{{ $student->student_status === 'Out' ? ($student->student_out_time ? $student->student_out_time->format('m/d/Y h:i A') : 'N/A') : 'N/A' }}</td>
            </tr>
            <tr>
                <th>Current Status</th>
                <td>{{ $student->student_status }}</td>
            </tr>
        </table>
        
        <div class="qr-code">
            <h2>Student QR Code</h2>
            <div id="qrcode"></div>
            <p>Scan this QR code at the gates for entry/exit.</p>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const qr = qrcode(0, 'M');
            qr.addData(JSON.stringify({ token: '{{ $student->unique_token }}' }));
            qr.make();
            document.getElementById('qrcode').innerHTML = qr.createImgTag(5, 10);
        });
    </script>
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
