@extends('layout')

@section('content')

<nav aria-label="breadcrumb">
  	<ol class="breadcrumb">
    	<li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    	<li class="breadcrumb-item active">Profile</li>
  	</ol>
</nav>

<div class="container mt-4">
	@if(session()->has('success'))
	<div class="alert alert-success">
		{{ session()->get('success') }}
	</div>
	@endif

	<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow-lg border-0 rounded-lg">
            <div class="card-header bg-success text-white text-center">
                <h4 class="mb-0">Edit Admin Information</h4>
            </div>
            <div class="card-body p-4">
                <form method="post" action="{{ route('profile.edit_validation') }}">
                    @csrf
                    <div class="form-group mb-4">
                        <label for="name" class="font-weight-bold"><b>User Name</b></label>
                        <input type="text" id="name" name="name" class="form-control rounded-pill" placeholder="Name" value="{{ $data->name }}" />
                        @if($errors->has('name'))
                        <span class="text-danger">{{ $errors->first('name') }}</span>
                        @endif
                    </div>
                    <div class="form-group mb-4">
                        <label for="email" class="font-weight-bold"><b>User Email</b></label>
                        <input type="email" id="email" name="email" class="form-control rounded-pill" placeholder="Email" value="{{ $data->email }}" />
                        @if($errors->has('email'))
                        <span class="text-danger">{{ $errors->first('email') }}</span>
                        @endif
                    </div>
                    <div class="form-group mb-4">
                        <label for="password" class="font-weight-bold"><b>Password</b></label>
                        <input type="password" id="password" name="password" class="form-control rounded-pill" placeholder="Password" />
                        @if($errors->has('password'))
                        <span class="text-danger">{{ $errors->first('password') }}</span>
                        @endif
                    </div>
                    <div class="form-group mb-3 text-center">
                        <input type="submit" class="btn btn-success rounded-pill w-100 py-2" value="Update Details" />
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
