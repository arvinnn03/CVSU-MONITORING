@extends('layout')

@section('content')

<h2 class="mt-3 text-center">Department & Office Management</h2>
<nav aria-label="breadcrumb">
  	<ol class="breadcrumb">
    	<li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    	<li class="breadcrumb-item"><a href="/department">Department & Office Management Table</a></li>
    	<li class="breadcrumb-item active">Add New Department</li>
  	</ol>
</nav>


<div class="row mt-4 justify-content-center">
    <div class="col-md-8 col-lg-6">
		<div class="card shadow-lg border-0 rounded-lg">
			<div class="card-header bg-success text-white text-center">
			<h4 class="mb-0">Add New Department/Office</h4>
		</div>
			<div class="card-body p-4">
				<form method="POST" action="{{ route('department.add_validation') }}">
					@csrf
					<div class="form-group mb-3">
		        		<label><b>Department/Office Name</b></label>
		        		<input type="text" name="department_name" class="form-control" />
		        		@if($errors->has('department_name'))
		        			<span class="text-danger">{{ $errors->first('department_name') }}</span>
		        		@endif
		        	</div>
		        	<div class="form-group mb-3">
		        		<label><b>Course</b></label>
		        		<div class="row">
		        			<div class="col-md-10">
		        				<input type="text" name="course_name[]" class="form-control" />
		        			</div>
		        			<div class="col-md-2">
		        				<button type="button" name="add_course" id="add_course" class="btn btn-success btn-sm">+</button>
		        			</div>
		        		</div>
		        		<div id="append_course"></div>
		        	</div>
		        	<div class="form-group mb-3 text-center">
		        		<input type="submit" class="form-control rounded-pill w-100 py-2 btn btn-success" value="Add" />
		        	</div>
				</form>
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function(){
	var count_course = 0;

	$(document).on('click', '#add_course', function(){
		count_course++;

		var html = `
		<div class="row mt-2" id="course_`+count_course+`">
			<div class="col-md-10">
				<input type="text" name="course_name[]" class="form-control department_course_name" />
			</div>
			<div class="col-md-2">
				<button type="button" name="remove_course" class="btn btn-danger btn-sm remove_course" data-id="`+count_course+`">-</button>
			</div>
		</div>
		`;

		$('#append_course').append(html);
	});

	$(document).on('click', '.remove_course', function(){
		var button_id = $(this).data('id');
		$('#course_'+button_id).remove();
	});
});
</script>
@endsection
