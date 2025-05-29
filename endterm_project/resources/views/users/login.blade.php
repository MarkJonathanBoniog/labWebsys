<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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

    .login-wrapper {
        background-color: rgba(255, 255, 255, 0.9);
        border-radius: 1rem;
        padding: 2.5rem;
        min-height: 500px;
        box-shadow: 0 0 25px rgba(0, 0, 0, 0.4);
    }
</style>

<div class="container vh-100 d-flex align-items-center">
    <div class="row justify-content-end w-100">
        <div class="col-md-5 login-wrapper">
            <h3 class="text-center fw-bold mb-2">Transfer Credential System</h3>
            <h4 class="text-center mb-4">Sign in</h4>

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

            <form class="mb-4" method="POST" action="{{ route('login.attempt') }}">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="text" class="form-control" id="email" name="email"
                           value="{{ old('email') }}">
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password"
                           name="password">
                </div>

                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-primary">Sign In</button>
                </div>
            </form>

            <div class="text-center mt-4">
                <a href="{{ route('register.staff') }}">Don't have an account? Register</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
