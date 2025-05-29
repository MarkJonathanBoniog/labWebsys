<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Staff</title>
    <link href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}" rel="stylesheet">
    <script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
</head>
<body>
<style>
    body {
        background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.7)), url('/images/psu-campus.jpg') no-repeat center center fixed;
        background-size: cover;
        color: #333;
    }

    .register-wrapper {
        background-color: rgba(255, 255, 255, 0.9);
        border-radius: 1rem;
        padding: 2.5rem;
        min-height: 550px;
        box-shadow: 0 0 25px rgba(0, 0, 0, 0.4);
    }
</style>

<div class="container d-flex align-items-center">
    <div class="row justify-content-end w-100">
        <div class="col-md-5 register-wrapper">
            <h4 class="text-center fw-bold mb-2">Transfer Credential System</h4>
            <h5 class="text-center mb-4">Staff Registration</h5>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register.staff.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="fname" class="form-label">First Name</label>
                    <input type="text" class="form-control" name="fname" value="{{ old('fname') }}">
                </div>

                <div class="mb-3">
                    <label for="mname" class="form-label">Middle Name (optional)</label>
                    <input type="text" class="form-control" name="mname" value="{{ old('mname') }}">
                </div>

                <div class="mb-3">
                    <label for="lname" class="form-label">Last Name</label>
                    <input type="text" class="form-control" name="lname" value="{{ old('lname') }}">
                </div>

                <div class="mb-3">
                    <label for="title" class="form-label">Title (optional)</label>
                    <input type="text" class="form-control" name="title" value="{{ old('title') }}" placeholder="eg. MIT">
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="text" class="form-control" name="email" value="{{ old('email') }}" placeholder="user@gmail.com">
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password (min. 6 chars)</label>
                    <input type="password" class="form-control" name="password">
                </div>

                <div class="mb-4">
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" name="password_confirmation">
                </div>

                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-primary">Register Staff</button>
                </div>
            </form>

            <div class="text-center">
                <a href="{{ route('login') }}">Already registered? Go to login</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
