document.addEventListener('DOMContentLoaded', () => {
    // DOM Elements
    const departmentSelect = document.getElementById('visitor_department');
    const meetPersonSelect = document.getElementById('visitor_meet_person_name');
    const statusSelect = document.getElementById('visitor_status');
    const enterDateTimeField = document.getElementById('visitor_enter_time');
    const exitDateTimeField = document.getElementById('visitor_out_time');

    // Format time with AM/PM
    const formatTimeWithAMPM = (hours, minutes) => {
        const period = hours >= 12 ? 'PM' : 'AM';
        const adjustedHours = hours % 12 || 12;
        return `${String(adjustedHours).padStart(2, '0')}:${String(minutes).padStart(2, '0')} ${period}`;
    };

    // Get current date and time
    const getCurrentDateTime = () => {
        const now = new Date();
        // Convert to Philippines time
        const philippinesTime = new Date(now.toLocaleString("en-US", {timeZone: "Asia/Manila"}));
        const date = [
            String(philippinesTime.getMonth() + 1).padStart(2, '0'),
            String(philippinesTime.getDate()).padStart(2, '0'),
            philippinesTime.getFullYear()
        ].join('/');

        const hours = philippinesTime.getHours();
        const minutes = philippinesTime.getMinutes();
        const time = formatTimeWithAMPM(hours, minutes);

        return `${date} ${time}`;
    };

    // Set current date and time in the specified field
    const setCurrentDateTime = (field) => {
        field.value = getCurrentDateTime();
        console.log(`${field.id} set to: ${field.value} at ${new Date().toISOString()}`);
    };

    // Initialize enter time field
    setCurrentDateTime(enterDateTimeField);

    // Handle department change
    departmentSelect.addEventListener('change', function () {
        const selectedDepartmentName = this.value;
        meetPersonSelect.innerHTML = '';

        if (selectedDepartmentName) {
            const departments = JSON.parse(this.dataset.departments);
            const department = departments[selectedDepartmentName];

            if (department) {
                department.contact_person.split(',').forEach(contact => {
                    const option = document.createElement('option');
                    option.value = contact.trim();
                    option.text = contact.trim();
                    meetPersonSelect.appendChild(option);
                });
            }
        }
    });

    // Handle status change
    statusSelect.addEventListener('change', function () {
        if (this.value === 'Out') {
            setCurrentDateTime(exitDateTimeField);
        } else {
            exitDateTimeField.value = '';
            console.log(`Exit datetime cleared at ${new Date().toISOString()}`);
        }
    });

    // Phone number input validation
    const phoneInput = document.getElementById('visitor_mobile_no');
    phoneInput.addEventListener('input', function(e) {
        // Remove non-digit characters
        this.value = this.value.replace(/\D/g, '');
        
        // Limit to 11 digits
        if (this.value.length > 11) {
            this.value = this.value.slice(0, 11);
        }
    });

    // DataTable Initialization
    $(document).ready(function() {
        const table = $('#visitor_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "/visitor/fetchall",
                type: 'GET',
                error: (xhr, error) => {
                    console.error('Error fetching data:', error);
                    alert('Failed to fetch data. Please try again.');
                }
            },
            columns: [
                { data: 'visitor_image', name: 'visitor_image', orderable: false, searchable: false },
                { data: 'visitor_name', name: 'visitor_name' },
                { data: 'visitor_meet_person_name', name: 'visitor_meet_person_name' },
                { data: 'visitor_department', name: 'visitor_department' },
                { data: 'visitor_enter_time', name: 'visitor_enter_time' },
                { data: 'visitor_out_time', name: 'visitor_out_time' },
                { data: 'visitor_status', name: 'visitor_status' },
                { data: 'name', name: 'name' },
                { data: 'action', name: 'action', orderable: false }
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
                paginate: { next: "Next", previous: "Previous" }
            }
        });

        // Handle delete action
        $(document).on('click', '.delete', function() {
            const id = $(this).data('id');
            const csrfToken = $('meta[name="csrf-token"]').attr('content');

            if (confirm("Are you sure you want to remove this visitor?")) {
                $.ajax({
                    url: `/visitor/delete/${id}`,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#visitor_table').DataTable().ajax.reload();
                            alert('Visitor deleted successfully.');
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                        alert('Error deleting visitor. Please try again.');
                    }
                });
            }
        });

        // Handle view image action
        $(document).on('click', '.view-image', function() {
            const imageUrl = $(this).data('image');
            $('#modalImage').attr('src', imageUrl);
            $('#imageModal').modal('show');
        });

        // Add event listener for name input
        $('#visitor_name').on('input', function() {
            const visitorName = $(this).val();
            if (visitorName.length >= 3) { // Only search if at least 3 characters are entered
                $.ajax({
                    url: '/get-visitor-details',
                    type: 'GET',
                    data: { name: visitorName },
                    success: function(response) {
                        $('#visitor_name').val(response.name);
                        $('#visitor_email').val(response.email);
                        $('#visitor_mobile_no').val(response.phone);
                    },
                    error: function() {
                        // Clear fields if no match found
                        $('#visitor_email').val('');
                        $('#visitor_mobile_no').val('');
                    }
                });
            }
        });
    });
});
