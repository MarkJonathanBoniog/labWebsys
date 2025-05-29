@extends('layouts.main')

@section('content')
<div class="mt-4">
    <h3>System Settings</h3>

    <hr class="mx-2">

    <div class="mb-5">
        <h5>Upload Header & Footer Images for PDF Documents</h5>

        @if(session('image_success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('image_success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->has('header') || $errors->has('footer'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @if ($errors->has('header'))
                        <li>{{ $errors->first('header') }}</li>
                    @endif
                    @if ($errors->has('footer'))
                        <li>{{ $errors->first('footer') }}</li>
                    @endif
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.systemSettings.uploadImages') }}" enctype="multipart/form-data">
            @csrf

            <!-- Header Image (full width) -->
            <div class="mb-4">
                <label class="form-label">Header Image</label>
                <input type="file" name="header" class="form-control">
                <div class="mt-2">
                    <img src="{{ $headerPath ?? 'https://via.placeholder.com/300x100?text=No+Header' }}" 
     alt="Header Preview" class="img-fluid border">
                </div>
            </div>

            <!-- Footer Image (full width) -->
            <div class="mb-4">
                <label class="form-label">Footer Image</label>
                <input type="file" name="footer" class="form-control">
                <div class="mt-2">
                    <img src="{{ $footerPath ?? 'https://via.placeholder.com/300x100?text=No+Footer' }}" 
     alt="Footer Preview" class="img-fluid border">
                </div>
            </div>

            <!-- Submit Button (centered) -->
            <div class="text-center">
                <button type="submit" class="btn btn-primary px-5">Upload Images</button>
            </div>
        </form>
    </div>

    <hr id="progs" class="mx-2">

    <h5>Manage Programs</h5>

    {{-- Alert Messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Add Program Form --}}
    <form method="POST" action="{{ route('admin.program.store') }}" class="mb-4">
        @csrf
        <div class="row g-2">
            <div class="col-md-4">
                <input type="text" name="abbrev" class="form-control @error('abbrev') is-invalid @enderror" placeholder="Abbreviation" value="{{ old('abbrev') }}" required>
            </div>
            <div class="col-md-6">
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Program Name" value="{{ old('name') }}" required>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Add</button>
            </div>
        </div>
    </form>

    {{-- Programs Table --}}
    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Abbreviation</th>
                <th>Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($programs as $index => $program)
                <tr>
                    <form method="POST" action="{{ route('admin.program.update', $program->id) }}">
                        @csrf
                        @method('PUT')
                        <td>{{ $index + 1 }}</td>
                        <td><input type="text" name="abbrev" value="{{ old('abbrev', $program->abbrev) }}" class="form-control" required></td>
                        <td><input type="text" name="name" value="{{ old('name', $program->name) }}" class="form-control" required></td>
                        <td class="d-flex gap-2">
                            <button type="submit" class="btn btn-sm btn-success">Update</button>
                    </form>
                            <form method="POST" action="{{ route('admin.program.destroy', $program->id) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center">No programs found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
