@extends('layout')

@section('content')

<h2 class="mt-3">Sub User Management</h2>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/sub_user">Sub Management</a></li>
        <li class="breadcrumb-item active" aria-current="page">Edit Sub User</li>
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
                    <h4 class="mb-0">Edit Sub User</h4>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('sub_user.edit_validation') }}">
                        @csrf
                        <div class="form-group mb-4">
                            <label for="name" class="font-weight-bold">User Name</label>
                            <input type="text" id="name" name="name" class="form-control rounded-pill" placeholder="Name" value="{{ old('name', $data->name) }}">
                            @error('name')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group mb-4">
                            <label for="email" class="font-weight-bold">User Email</label>
                            <input type="email" id="email" name="email" class="form-control rounded-pill" placeholder="Email" value="{{ old('email', $data->email) }}">
                            @error('email')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group mb-4">
                            <label for="password" class="font-weight-bold">Password</label>
                            <input type="password" id="password" name="password" class="form-control rounded-pill" placeholder="Password">
                            @error('password')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group mb-3 text-center">
                            <input type="hidden" name="hidden_id" value="{{ $data->id }}">
                            <button type="submit" class="btn btn-success rounded-pill w-100 py-2">Update Details</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
