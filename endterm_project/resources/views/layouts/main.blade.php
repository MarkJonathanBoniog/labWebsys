<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Dashboard')</title>
    <link href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}" rel="stylesheet">
    <script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            overflow-x: auto; /* Enable horizontal scroll */
            background-color: #ffffff;
        }

        .fixed-layout {
            width: 1248px;
            display: flex;
            height: 100vh;
        }

        .sidebar {
            width: 250px;
            background-color: #003366;
            padding: 20px;
            border-right: 1px solid #ccc;
        }

        .sidebar h4 {
            color: #ffffff;
            margin-top: 0;
        }

        .sidebar a {
            display: block;
            padding: 10px;
            margin-bottom: 10px;
            text-decoration: none;
            color: #ffffff; /* white links for contrast */
            border-radius: 4px;
        }

        .sidebar a:hover {
            background-color: #0055aa;
            color: #ffffff;
        }

        .nav-link.active {
            font-weight: bold;
            color: #0d1a26; /* dark text on light background */
            background-color: #cce6ff; /* soft blue highlight */
            border-radius: 4px;
        }

        .content-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .header {
            background-color: #fff9cc; /* pastel yellow */
            color: #000000; /* dark text */
            padding: 15px 20px;
            border-bottom: 1px solid #ccc;
        }

        .main-content {
            padding: 20px;
            flex: 1;
            overflow-y: auto;
            background-color: #ffffff;
        }
    </style>
</head>
<body style="margin: 0; overflow-x: auto;">
    <div class="fixed-layout">

        <!-- Sidebar -->
        <div class="sidebar">
            <h4>Tranfer Credential System</h4>
            <hr class="mx-3">
            <ul class="nav flex-column">
                @if (Auth::user()->role == "staff")
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('staff.dashboard') ? 'active' : '' }}" href="{{ route('staff.dashboard') }}">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('records.index') ? 'active' : '' }}" href="{{ route('records.index') }}">Record List</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('staff.audit') ? 'active' : '' }}" href="{{ route('staff.audit') }}">Audit Tracing</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('staff.settings') ? 'active' : '' }}" href="{{ route('staff.settings') }}">User Settings</a>
                </li>
                @endif

                @if (Auth::user()->role == "admin")
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('admin.index') ? 'active' : '' }}" href="{{ route('admin.index') }}">Record List</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('admin.audit') ? 'active' : '' }}" href="{{ route('admin.audit') }}">Staff Audit Tracing</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('admin.systemSettings') ? 'active' : '' }}" href="{{ route('admin.systemSettings') }}">Manage System Settings</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('admin.settings') ? 'active' : '' }}" href="{{ route('admin.settings') }}">User Settings</a>
                </li>
                @endif

                <li class="nav-item mt-3">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="btn btn-danger w-100" type="submit">Logout</button>
                    </form>
                </li>
            </ul>
        </div>

        <!-- Content -->
        <div class="content-wrapper">
            <div class="header d-flex justify-content-between align-items-start">
                <div>
                    <div><strong>Welcome, {{ Auth::user()->fname }} {{ Auth::user()->lname }}!</strong></div>
                    <div>
                        Transfer Credential System  
                        @if (Auth::user()->role == "admin")
                            Administrator
                        @else
                            Staff
                        @endif
                    </div>
                </div>
                <div id="current-time"></div>
            </div>


            <div class="main-content">
                @yield('content')
            </div>
        </div>
    </div>
</body>
<script>
    function updateTime() {
        const options = {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: 'numeric',
            minute: '2-digit',
            second: '2-digit',
            hour12: true
        };
        const now = new Date();
        const formattedTime = now.toLocaleString('en-US', options);
        document.getElementById('current-time').textContent = formattedTime;
    }

    setInterval(updateTime, 1000);
    updateTime(); // Initial call
</script>
@yield('scripts')
</html>
