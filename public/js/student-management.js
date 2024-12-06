$(document).ready(function() {
    // DataTable Initialization
    const table = $('#student_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "/student/fetchall",
            type: 'GET',
            error: (xhr, error) => {
                console.error('Error fetching data:', error);
                alert('Failed to fetch student data. Please try again.');
            }
        },
        columns: [
            { data: 'stud_id', name: 'stud_id' },
            { data: 'student_department', name: 'student_department' },
            { data: 'student_course', name: 'student_course' },
            { 
                data: 'student_enter_time', 
                name: 'student_enter_time',
                render: function(data) {
                    return data || 'N/A';
                }
            },
            { 
                data: 'student_out_time', 
                name: 'student_out_time',
               render: function(data, type, row) {
                     // Check the status and render accordingly
                    return row.student_status === 'In' ? 'N/A' : (data || 'N/A');
                }
            },
            { data: 'student_status', name: 'student_status' },
            { data: 'action', name: 'action', orderable: false }

            // Removed action button column
        ],
        responsive: true,
        scrollY: '50vh',
        scrollCollapse: true,
        paging: true,
        lengthChange: false,
        pageLength: 10,
        // Move the search box to the right side
        dom: '<"row"<"col-md-6"l><"col-md-6"f>>rtip',
        language: {
            search: "Search:",
            paginate: { next: "Next", previous: "Previous" }
        }
    });

    // QR Code button click handler
    $(document).on('click', '.qr-code-btn', function(e) {
        e.preventDefault();
        var studentId = $(this).data('id');
        
        // Fetch the unique token for this student
        $.ajax({
            url: '/student/get-token/' + studentId,
            type: 'GET',
            success: function(response) {
                if (response.token) {
                    var qr = qrcode(0, 'M');
                    qr.addData(JSON.stringify({ token: response.token }));
                    qr.make();

                    var modal = `
                        <div class="modal fade" id="qrCodeModal" tabindex="-1" aria-labelledby="qrCodeModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="qrCodeModalLabel">Student QR Code</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body text-center">
                                        ${qr.createImgTag(5)}
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;

                    $('body').append(modal);
                    var qrCodeModal = new bootstrap.Modal(document.getElementById('qrCodeModal'));
                    qrCodeModal.show();

                    // Remove the modal from the DOM when it's hidden
                    $('#qrCodeModal').on('hidden.bs.modal', function () {
                        $(this).remove();
                    });
                } else {
                    alert('Error: Unable to generate QR code');
                }
            },
            error: function() {
                alert('Error: Unable to fetch student token');
            }
        });
    });

    // Delete button click handler
    $(document).on('click', '.delete-student', function() {
        var studentId = $(this).data('id');
        if (confirm('Are you sure you want to delete this student?')) {
            $.ajax({
                url: '/student/delete/' + studentId,
                type: 'DELETE',
                data: {
                    "_token": $('meta[name="csrf-token"]').attr('content'),
                },
                success: function(result) {
                    alert('Student deleted successfully');
                    table.ajax.reload();
                },
                error: function(xhr) {
                    alert('Error deleting student: ' + xhr.responseText);
                }
            });
        }
    });

    function updateStudentForm(data) {
        $('#stud_id').val(data.stud_id);
        $('#student_department').val(data.student_department);
        $('#student_course').val(data.student_course);
        $('#student_status').val(data.status);
        $('#student_enter_time').val(data.student_enter_time || 'N/A');
        $('#student_out_time').val(data.student_out_time || 'N/A');
    }

    // Handle manual verification
    $('#verifyButton').on('click', function() {
        var studentId = $('#student_id_scan').val();
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

    // Enforce number-only input for student ID
    $('#student_id_scan').on('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    // Initialize Cleave.js for student ID input
    new Cleave('#student_id_scan', {
        numeral: true,
        numeralThousandsGroupStyle: 'none',
        numeralIntegerScale: 10 // adjust this to match your max student ID length
    });

    // Start the QR scanner
    // startScanner();
});
