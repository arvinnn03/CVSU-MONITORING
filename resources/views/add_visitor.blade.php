@extends('layout')

@section('title', 'Add Visitor')

@section('content')

<!-- Breadcrumb Navigation -->
<nav aria-label="breadcrumb">
    <ol class="breadcrumb mt-2">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/visitor">Visitor Management</a></li>
        <li class="breadcrumb-item active" aria-current="page">Add Visitor</li>
    </ol>
</nav>

<div class="card">
    <div class="card-header">
        <h4 class="mb-0">Add Visitor</h4>
    </div>
    
    <div class="card-body">
        <form id="addVisitorForm" method="POST" action="{{ route('visitor.add_visitor_validation') }}" enctype="multipart/form-data">
            @csrf

            <div class="row">
                <!-- Left Column -->
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="visitor_name">Visitor Name</label>
                        <input type="text" name="visitor_name" id="visitor_name" class="form-control" placeholder="Visitor Name" />
                        @error('visitor_name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="visitor_email">Visitor Email</label>
                        <input type="email" name="visitor_email" id="visitor_email" class="form-control" placeholder="Visitor Email" />
                        @error('visitor_email')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="visitor_mobile_no">Visitor Phone</label>
                        <input type="text" name="visitor_mobile_no" id="visitor_mobile_no" class="form-control" placeholder="Visitor Phone" />
                        @error('visitor_mobile_no')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="visitor_department">Department</label>
                        <select name="visitor_department" id="visitor_department" class="form-control">
                            <option value="">Select Department</option>
                            @foreach($data as $department)
                                <option value="{{ $department->department_name }}">{{ $department->department_name }}</option>
                            @endforeach
                        </select>
                        @error('visitor_department')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="visitor_image">Image <span class="text-danger">*</span></label>
                        <input type="file" name="visitor_image" class="form-control" accept="image/*">
                    </div>
                </div>

                <!-- Right Column -->
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="visitor_meet_person_name">Person To Visit</label>
                        <input type="text" name="visitor_meet_person_name" id="visitor_meet_person_name" class="form-control" placeholder="Person To Visit" />
                        @error('visitor_meet_person_name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="visitor_reason_to_meet">Purpose</label>
                        <input type="text" name="visitor_reason_to_meet" id="visitor_reason_to_meet" class="form-control" placeholder="Purpose" />
                        @error('visitor_reason_to_meet')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="visitor_enter_time">Time In</label>
                        <input type="text" name="visitor_enter_time" id="visitor_enter_time" class="form-control" readonly />
                        @error('visitor_enter_time')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="visitor_out_time">Exit Time</label>
                        <input type="text" name="visitor_out_time" id="visitor_out_time" class="form-control" readonly />
                        @error('visitor_out_time')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="visitor_status">Status</label>
                        <select name="visitor_status" id="visitor_status" class="form-control">
                            <option value="-" disabled selected>-</option>
                            <option value="In">In</option>
                            <option value="Out">Out</option>
                        </select>
                        @error('visitor_status')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <input type="hidden" name="visitor_enter_by" value="{{ auth()->id() }}" />
            <button type="submit" class="btn btn-primary">Add</button>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const departmentSelect = document.getElementById('visitor_department');
        const meetPersonSelect = document.getElementById('visitor_meet_person_name');
        const statusSelect = document.getElementById('visitor_status');
        const enterDateTimeField = document.getElementById('visitor_enter_time');
        const exitDateTimeField = document.getElementById('visitor_out_time');

        const formatTimeWithAMPM = (hours, minutes) => {
            const period = hours >= 12 ? 'PM' : 'AM';
            const adjustedHours = hours % 12 || 12;
            return `${String(adjustedHours).padStart(2, '0')}:${String(minutes).padStart(2, '0')} ${period}`;
        };

        const getCurrentDateTime = () => {
            const now = new Date();
            const date = [
                String(now.getMonth() + 1).padStart(2, '0'),
                String(now.getDate()).padStart(2, '0'),
                now.getFullYear()
            ].join('/');

            const hours = now.getHours();
            const minutes = now.getMinutes();
            const time = formatTimeWithAMPM(hours, minutes);

            return `${date} ${time}`;
        };

        const setCurrentDateTime = (field) => {
            field.value = getCurrentDateTime();
        };

        setCurrentDateTime(enterDateTimeField);

        departmentSelect.addEventListener('change', function () {
            const selectedDepartmentName = this.value;
            meetPersonSelect.innerHTML = '';

            if (selectedDepartmentName) {
                const departments = @json($data->keyBy('department_name')->toArray());
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

        statusSelect.addEventListener('change', function () {
            if (this.value === 'Out') {
                setCurrentDateTime(exitDateTimeField);
            } else {
                exitDateTimeField.value = '';
            }
        });
    });
</script>

@endsection
