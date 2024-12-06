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
            verifyToken(data.token);
            verifyStudent(data.token);
            
            // Update visitor_enter_by based on the current user
            data.visitor_enter_by = $('meta[name="user-id"]').attr('content'); // Get user ID from meta tag
            updateVisitorEntry(data); // Call the function to update the visitor entry
        } catch (error) {
            console.error('Error parsing QR code data:', error);
            $('#feedback').text('Invalid QR code format.');
        }
    }

    function updateVisitorEntry(data) {
        $.ajax({
            url: '/visitor/update-entry', // Define the route for updating the visitor entry
            type: 'POST',
            data: {
                visitor_id: data.id, // Assuming the visitor ID is part of the scanned data
                visitor_enter_by: data.visitor_enter_by,
                _token: $('meta[name="csrf-token"]').attr('content') // CSRF token for security
            },
            success: function(response) {
                console.log('Visitor entry updated:', response);
            },
            error: function() {
                console.error('Error updating visitor entry');
                $('#feedback').text('Error updating visitor entry.');
            }
        });
    }

    function verifyToken(token) {
        console.log('Verifying token:', token);
        $.ajax({
            url: '/verify-token',
            type: 'POST',
            data: {
                token: token,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: handleVisitorVerificationResponse,
            error: function() {
                console.error('Error verifying token');
                $('#feedback').text('Error verifying token.');
            }
        });
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
            success: handleStudentVerificationResponse,
            error: function() {
                console.error('Error verifying student');
                $('#feedback').text('Error verifying student.');
            }
        });
    }


    function handleStudentVerificationResponse(response) {
        console.log('Verification response:', response);
        if (response.valid) {
            updateStudentStatus(response);
            showStudentStatusModal(response);
        } else {
            $('#feedback').text(response.message || 'Invalid QR.');
        }
    }
    function handleVisitorVerificationResponse(response) {
        console.log('Verification response:', response);
        if (response.valid) {
            updateVisitorStatus(response);
            showVisitorStatusModal(response);
        } else {
            $('#feedback').text(response.message || 'Invalid QR.');
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


    function updateVisitorStatus(response) {
        // Update the student table or any other UI elements as needed
        // This function will depend on how you want to display the updated student information
        console.log('Updating visitor status:', response);
        // Reload the DataTable to reflect changes
        if ($.fn.DataTable.isDataTable('#visitor_table')) {
            $('#visitor_table').DataTable().ajax.reload();
        }
    }

   
    function showVisitorStatusModal(response) {
        let statusText = `Visitor ${response.status} at ${response.status === 'In' ? response.visitor_enter_time : response.visitor_out_time}`;
        
        // Set status message
        $('#statusMessage').text(statusText);
    
        // Remove any previous status classes
        $('.modal-content').removeClass('bg-success-custom bg-alert-custom');
    
        // Toggle icons based on status and change background color
        if (response.status === 'In') {
            $('#icon-in').show();
            $('#icon-out').hide();
            $('.modal-content').addClass('bg-success-custom'); // Green background
        } else if (response.status === 'Out') {
            $('#icon-in').hide();
            $('#icon-out').show();
            $('.modal-content').addClass('bg-alert-custom'); // Red background with transparency
        }
    
        // Show the modal
        $('#statusUpdateModal').modal('show');
        
        // Hide the modal after 2 seconds
        setTimeout(function() {
            $('#statusUpdateModal').modal('hide');
        }, 2000);
    }
    
    function showStudentStatusModal(response) {
        let statusText = `Student ${response.stud_id} ${response.status} at ${response.status === 'In' ? response.student_enter_time : response.student_out_time}`;
        
        // Set status message
        $('#statusMessage').text(statusText);
    
        // Remove any previous status classes
        $('.modal-content').removeClass('bg-success-custom bg-alert-custom');
    
        // Toggle icons based on status and change background color
        if (response.status === 'In') {
            $('#icon-in').show();
            $('#icon-out').hide();
            $('.modal-content').addClass('bg-success-custom'); // Green background
        } else if (response.status === 'Out') {
            $('#icon-in').hide();
            $('#icon-out').show();
            $('.modal-content').addClass('bg-alert-custom'); // Red background with transparency
        }
    
        // Show the modal
        $('#statusUpdateModal').modal('show');
        
        // Hide the modal after 2 seconds
        setTimeout(function() {
            $('#statusUpdateModal').modal('hide');
        }, 2000);
    }
    
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
