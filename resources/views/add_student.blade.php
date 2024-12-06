@extends('layout')

@section('content')

<h2 class="mt-3">Student Management</h2>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/student">Student Management</a></li>
        <li class="breadcrumb-item active">Add New Student</li>
    </ol>
</nav>

<div class="row mt-4 justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow-lg border-0 rounded-lg">
            <div class="card-header bg-success text-white text-center">
                <h4 class="mb-0">Add New Student</h4>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('student.add_validation') }}">
                    @csrf
                    <div class="form-group mb-3">
                        <label><b>Student ID</b></label>
                        <input type="text" class="form-control rounded-pill" name="stud_id" placeholder="Enter Student ID" required>
                        @if($errors->has('stud_id'))
                            <span class="text-danger">{{ $errors->first('stud_id') }}</span>
                        @endif
                    </div>
                    <div class="form-group mb-3">
                        <label><b>Department</b></label>
                        <select name="student_department" id="student_department" class="form-control rounded-pill" required>
                            <option value="">Select Department</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->department_name }}">{{ $department->department_name }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('student_department'))
                            <span class="text-danger">{{ $errors->first('student_department') }}</span>
                        @endif
                    </div>
                    <div class="form-group mb-3">
                        <label><b>Course</b></label>
                        <select name="student_course" id="student_course" class="form-control rounded-pill" required>
                            <option value="">Select Course</option>
                        </select>
                        @if($errors->has('student_course'))
                            <span class="text-danger">{{ $errors->first('student_course') }}</span>
                        @endif
                    </div>
                    <div class="form-group mb-3 text-center">
                        <input type="submit" class="rounded-pill w-100 py-2 btn btn-success" value="Add">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let departmentSelect = document.getElementById('student_department');
    let courseSelect = document.getElementById('student_course');

    departmentSelect.addEventListener('change', function () {
        let selectedDepartmentName = this.value;
        courseSelect.innerHTML = '<option value="">Select Course</option>'; // Reset course options

        if (selectedDepartmentName) {
            // Retrieve the corresponding department from the JSON data
            let department = @json($departments->keyBy('department_name')->toArray())[selectedDepartmentName];
            if (department && department.course_name) {
                // Split the course_name by comma and create option elements
                department.course_name.split(',').forEach(function (course) {
                    let option = document.createElement('option');
                    option.value = course.trim();
                    option.text = course.trim();
                    courseSelect.appendChild(option);
                });
            }
        }
    });
});
</script>

@endsection
