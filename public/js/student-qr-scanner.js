$(document).ready(function() {
    let video = document.getElementById('preview');
    let canvasElement = document.getElementById('qr-canvas');
    let canvas = canvasElement.getContext('2d');
    let scanning = false;
    let lastScanTime = 0;
    const scanCooldown = 3000; // 3 seconds cooldown between scans

    function startScanner() {
        navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } })
            .then(function(stream) {
                video.srcObject = stream;
                video.setAttribute("playsinline", true);
                video.play();
                requestAnimationFrame(tick);
            })
            .catch(function(error) {
                console.error('Error accessing camera:', error);
                $('#feedback').text('Error accessing camera: ' + error.message);
            });
    }

    function tick() {
        if (video.readyState === video.HAVE_ENOUGH_DATA) {
            canvasElement.height = video.videoHeight;
            canvasElement.width = video.videoWidth;
            canvas.drawImage(video, 0, 0, canvasElement.width, canvasElement.height);
            var imageData = canvas.getImageData(0, 0, canvasElement.width, canvasElement.height);
            var code = jsQR(imageData.data, imageData.width, imageData.height, {
                inversionAttempts: "dontInvert",
            });
            if (code && !scanning) {
                scanning = true;
                handleScan(code.data);
                setTimeout(() => { scanning = false; }, scanCooldown);
            }
        }
        requestAnimationFrame(tick);
    }

    function handleScan(content) {
        const currentTime = new Date().getTime();
        if (currentTime - lastScanTime < scanCooldown) {
            console.log('Scan cooldown in effect');
            return;
        }
        lastScanTime = currentTime;

        console.log('QR code scanned:', content);
        try {
            const data = JSON.parse(content);
            verifyStudent(data.token);
        } catch (error) {
            console.error('Error parsing QR code data:', error);
            $('#feedback').text('Invalid QR code format.');
        }
    }

    function verifyStudent(token) {
        console.log('Verifying student token:', token);
        $.ajax({
            url: '/student/verify',
            type: 'POST',
            data: {
                token: token,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: handleVerificationResponse,
            error: function() {
                console.error('Error verifying student');
                $('#feedback').text('Error verifying student.');
            }
        });
    }

    function handleVerificationResponse(response) {
        console.log('Verification response:', response);
        if (response.valid) {
            updateStudentStatus(response);
            showStatusModal(response);
        } else {
            $('#feedback').text(response.message || 'Invalid student.');
        }
    }

    function updateStudentStatus(response) {
        // Update the student table or any other UI elements as needed
        // This function will depend on how you want to display the updated student information
        console.log('Updating student status:', response);
        // Reload the DataTable to reflect changes
        if ($.fn.DataTable.isDataTable('#student_table')) {
            $('#student_table').DataTable().ajax.reload();
        }
    }

    function showStatusModal(response) {
        let statusText = `Student ${response.stud_id} ${response.status} at ${response.status === 'In' ? response.student_enter_time : response.student_out_time}`;
        $('#statusMessage').text(statusText);
        $('#statusUpdateModal').modal('show');
        
        setTimeout(function() {
            $('#statusUpdateModal').modal('hide');
        }, 2000);
    }

    // Start the scanner
    startScanner();

    // Handle manual student ID verification
    $('#verifyButton').on('click', function() {
        let studentId = $('#student_id_scan').val();
        if (studentId) {
            verifyStudent(studentId);
        } else {
            alert('Please enter a Student ID');
        }
    });

    // Handle Enter key press in the input field
    $('#student_id_scan').on('keyup', function(e) {
        if (e.key === 'Enter' || e.keyCode === 13) {
            $('#verifyButton').click();
        }
    });
});
