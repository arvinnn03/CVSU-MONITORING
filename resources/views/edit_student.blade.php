@extends('layout')

@section('content')


<!-- Include Custom CSS -->
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css">
<!-- <link rel="stylesheet" href="{{ asset('css/sub_user.css') }}"> -->



<h2 class="mt-3">Student Management</h2>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/student">Student Management</a></li>
        <li class="breadcrumb-item active" aria-current="page">Edit Student</li>
    </ol>
</nav>

<div class="container mt-4">
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-success text-white text-center">
                    <h4 class="mb-0">Edit Student</h4>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('student.update', $student->id) }}">
                        @csrf
                        @method('POST')

                        <!-- Student ID -->
                        <div class="form-group mb-4">
                            <label for="stud_id" class="font-weight-bold"><b>Student ID</b></label>
                            <input type="text" id="stud_id" name="stud_id" class="form-control rounded-pill" value="{{ old('stud_id', $student->stud_id) }}" required>
                            @error('stud_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Department -->
                        <div class="form-group mb-4">
                            <label for="student_department" class="font-weight-bold"><b>Department</b></label>
                            <select id="student_department" name="student_department" class="form-select rounded-pill" required>
                                <option value="">Select Department</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->department_name }}" {{ old('student_department', $student->student_department) == $department->department_name ? 'selected' : '' }}>
                                        {{ $department->department_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('student_department')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Course -->
                        <div class="form-group mb-4">
                            <label for="student_course" class="font-weight-bold"><b>Course</b></label>
                            <input type="text" id="student_course" name="student_course" class="form-control rounded-pill" value="{{ old('student_course', $student->student_course) }}" required>
                            @error('student_course')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="form-group mb-3 text-center">
                            <button type="submit" class="btn btn-success rounded-pill w-100 py-2">Update Student</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
