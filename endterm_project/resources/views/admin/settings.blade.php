@extends('layouts.main')

@section('content')
<div class="mt-4">
    <h3>Account Settings</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.settings.update') }}">
        @csrf

        <div class="mb-3">
            <label for="fname" class="form-label">Office Name</label>
            <input type="text" name="fname" class="form-control @error('fname') is-invalid @enderror" value="{{ old('fname', auth()->user()->fname) }}">
            @error('fname') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <!-- <label for="mname" class="form-label">Middle Name (optional)</label> -->
            <input type="hidden" name="mname" class="form-control @error('mname') is-invalid @enderror" value="{{ old('mname', auth()->user()->mname) }}">
            @error('mname') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="lname" class="form-label">Suffix</label>
            <input type="text" name="lname" class="form-control @error('lname') is-invalid @enderror" value="{{ old('lname', auth()->user()->lname) }}">
            @error('lname') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <!-- <label for="title" class="form-label">Title (optional)</label> -->
            <input type="hidden" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', auth()->user()->title) }}">
            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', auth()->user()->email) }}">
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <button type="submit" class="btn btn-primary">Update Profile</button>
    </form>

    <hr>

    <h3>Update Password</h3>
    <form method="POST" action="{{ route('staff.settings.update') }}">
     @csrf
        <div class="mb-3">
            <label for="password" class="form-label">New Password</label>
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Confirm New Password</label>
            <input type="password" name="password_confirmation" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">Update Password</button>
    </form>
</div>
@endsection
