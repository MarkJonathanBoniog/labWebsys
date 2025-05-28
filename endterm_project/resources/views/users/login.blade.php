<div class="container">
    <h2>Login</h2>

    @if (session('success'))
        <div>
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login.attempt') }}">
        @csrf

        <div>
            <label for="email">Email Address</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
        </div>

        <div>
            <label for="password">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <button type="submit">Login</button>
    </form>
</div>


<a href="{{ route('register.staff') }}">Go to registration</a>
