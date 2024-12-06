@extends('layout')

@section('content')

<h2 class="mt-3">Department Management</h2>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/department">Department Management</a></li>
        <li class="breadcrumb-item active" aria-current="page">Edit Department</li>
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
                    <h4 class="mb-0">Edit Department</h4>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('department.edit_validation') }}">
                        @csrf

                        <!-- Department Name -->
                        <div class="form-group mb-4">
                            <label for="department_name" class="font-weight-bold"><b>Department Name</b></label>
                            <input type="text" id="department_name" name="department_name" class="form-control rounded-pill" value="{{ old('department_name', $data->department_name) }}" />
                            @error('department_name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Course List -->
                        <div class="form-group mb-4">
                            <label for="course_name" class="font-weight-bold"><b>Course</b></label>
                            
                            @php
                                $courseNames = explode(', ', $data->course_name);
                            @endphp

                            @foreach($courseNames as $index => $courseName)
                                <div class="row mt-2" id="course_{{ $index }}">
                                    <div class="col-md-10">
                                        <input type="text" name="course_name[]" class="form-control rounded-pill" value="{{ $courseName }}" />
                                    </div>
                                    <div class="col-md-2">
                                        @if($index === 0)
                                            <button type="button" id="add_course" class="btn btn-success btn-sm rounded-pill">+</button>
                                        @else
                                            <button type="button" class="btn btn-danger btn-sm rounded-pill remove_course" data-id="{{ $index }}">-</button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach

                            <div id="append_course"></div>
                        </div>

                        <!-- Submit Button -->
                        <div class="form-group mb-3 text-center">
                            <input type="hidden" name="hidden_id" value="{{ $data->id }}" />
                            <input type="hidden" id="total_course_name" value="{{ count($courseNames) }}" />
                            <button type="submit" class="btn btn-success rounded-pill w-100 py-2">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    let countCourse = $('#total_course_name').val();

    // Add Course
    $(document).on('click', '#add_course', function() {
        countCourse++;
        $('#append_course').append(`
            <div class="row mt-2" id="course_${countCourse}">
                <div class="col-md-10">
                    <input type="text" name="course_name[]" class="form-control rounded-pill" />
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-sm rounded-pill remove_course" data-id="${countCourse}">-</button>
                </div>
            </div>
        `);
    });

    // Remove Course
    $(document).on('click', '.remove_course', function() {
        let buttonId = $(this).data('id');
        $(`#course_${buttonId}`).remove();
    });
});
</script>
@endpush

@endsection
