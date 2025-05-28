<div class="container">
    <h2>Register Staff</h2>

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

    <form action="{{ route('register.staff.store') }}" method="POST">
        @csrf

        <div>
            <label for="fname">First Name</label>
            <input type="text" class="form-control" name="fname" value="{{ old('fname') }}" required>
        </div>

        <div>
            <label for="lname">Last Name</label>
            <input type="text" class="form-control" name="lname" value="{{ old('lname') }}" required>
        </div>

        <div>
            <label for="email">Email Address</label>
            <input type="email" class="form-control" name="email" value="{{ old('email') }}" required>
        </div>

        <div>
            <label for="password">Password (min. 6 chars)</label>
            <input type="password" class="form-control" name="password" required>
        </div>

        <div>
            <label for="password_confirmation">Confirm Password</label>
            <input type="password" name="password_confirmation" required>
        </div>

        <button type="submit">Register Staff</button>
    </form>
</div>

<a href="{{ route('login') }}">Go to login</a>
