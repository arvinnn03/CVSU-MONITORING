@extends('layout')

@section('content')

<h2 class="mt-3 text-center">Sub User Management</h2>
<nav aria-label="breadcrumb">
  	<ol class="breadcrumb">
    	<li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    	<li class="breadcrumb-item"><a href="/sub_user">Sub User Management Table</a></li>
    	<li class="breadcrumb-item active">Add New Sub User</li>
  	</ol>
</nav>
<div class="row mt-4 justify-content-center">
    <div class="col-md-8 col-lg-6">
		<div class="card shadow-lg border-0 rounded-lg">
			<div class="card-header bg-success text-white text-center">
			<h4 class="mb-0">Add New User</h4>
		</div>
				<div class="card-body p-4">
				<form method="POST" action="{{ route('sub_user.add_validation') }}">
					@csrf
					<div class="form-group mb-3">
						<label><b>User Name</b></label>
						<input type="text" name="name" class="form-control" placeholder="Name" />
						@if($errors->has('name'))
							<span class="text-danger">{{ $errors->first('name') }}</span>
						@endif
					</div>
					<div class="form-group mb-3">
						<label><b>User Email</b></label>
						<input type="text" name="email" class="form-control" placeholder="Email">
						@if($errors->has('email'))
							<span class="text-danger">{{ $errors->first('email') }}</span>
						@endif
					</div>
					<div class="form-group mb-3">
						<label><b>Password</b></label>
						<input type="password" name="password" class="form-control" placeholder="Password">
						@if($errors->has('password'))
							<span class="text-danger">{{ $errors->first('password') }}</span>
						@endif
					</div>
					<div class="form-group mb-3">
						<label for="type">User Type</label>
						<select name="type" id="type" class="form-control" required>
							<option value="User1">User1</option>
							<option value="User2">User2</option>
							<option value="User3">User3</option>
						</select>
						@if($errors->has('type'))
							<span class="text-danger">{{ $errors->first('type') }}</span>	
						@endif
					</div>					
					<div class="form-group mb-3 text-center">
						<input type="submit" class="rounded-pill w-100 py-2 btn btn-success" value="Add" />
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

@endsection
