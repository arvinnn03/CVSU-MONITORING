<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor Management System</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    @guest
        @yield('content')
    @else
    <!-- Scripts -->
    <script src="{{ asset('js/jquery.js') }}"></script>
    <script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.js') }}"></script>
    <script src="{{ asset('js/dataTables.bootstrap5.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#user_table').DataTable();

        });
    </script>

    <!-- Header -->
    <header class="navbar navbar-dark sticky-top green-bg flex-md-nowrap p-0 shadow">
        <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3 d-flex align-items-center" href="/dashboard">
            <!-- Logo -->
            <img src="{{ asset('images/cvsu.png') }}" alt="CvSU Logo" class="logo">
            <span class="ms-2 text-white">CvSU Main</span>
        </a>
        <button class="navbar-toggler d-md-none" type="button" id="sidebarToggle">
            <i class="fa fa-bars"></i>
        </button>
        <div class="navbar-nav ms-auto">    
            <div class="nav-item text-nowrap">
                <a class="nav-link px-3 text-white" href="{{ route('profile') }}">Welcome, {{ Auth::user()->email }}</a>
            </div>
        </div>
    </header>

   <!-- Sidebar -->
    <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
        <div class="position-sticky pt-3">
            <ul class="nav flex-column">
                
                <li class="nav-item">
                    <a class="nav-link {{ Request::segment(1) == 'dashboard' ? 'active' : '' }}" href="/dashboard">
                        <!-- <i class="fas fa-tachometer-alt"></i>  -->
                    </a>
                </li>
                @if(Auth::user()->type == 'Admin')
                <li class="nav-item">
                    <a class="nav-link {{ Request::segment(1) == 'profile' ? 'active' : '' }}" href="/profile">
                        <i class="fas fa-user"></i> Profile
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link {{ Request::segment(1) == 'sub_user' ? 'active' : '' }}" href="/sub_user">
                        <i class="fas fa-users"></i> Sub User
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link {{ Request::segment(1) == 'department' ? 'active' : '' }}" href="/department">
                        <i class="fas fa-building"></i> Department
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link {{ Request::segment(1) == 'student' ? 'active' : '' }}" href="/student">
                        <i class="fas fa-user-graduate"></i> Student
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ Request::segment(1) == 'otp_form' ? 'active' : '' }}" href="/otp_form">
                        <i class="fas fa-user-graduate"></i> OTP Request
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ Request::segment(1) == 'advance_sched' ? 'active' : '' }}" href="/advance_sched">
                        <i class="fas fa-user-graduate"></i> Advance Schedule
                    </a>
                </li>
                
                <!-- Visitor Menu with Submenu -->
                <li class="nav-item">
                    <a class="nav-link {{ Request::segment(1) == 'visitor' ? 'active' : '' }}" href="#visitorSubmenu" data-bs-toggle="collapse" aria-expanded="false">
                        <i class="fas fa-user-check"></i> Visitor
                    </a>
                    <ul class="collapse {{ Request::segment(1) == 'visitor' ? 'show' : '' }}" id="visitorSubmenu">
                        <li>
                            <a class="nav-link" href="/visitor">Manage Visitors</a>
                        </li>
                @endif
                        
                        <!-- Show GATE 1 only to Admin and User1 -->
                        @if(Auth::user()->type == 'Admin' || Auth::user()->type == 'User1')
                        <li>
                            <a class="nav-link" href="/gate1_visitor/verify">GATE 1</a>
                        </li>
                        @endif
                        @if(Auth::user()->type == 'Admin' || Auth::user()->type == 'User2')
                        <li>
                            <a class="nav-link" href="/gate2_visitor/verify">GATE 2</a>
                        </li>
                        @endif
                        @if(Auth::user()->type == 'Admin' || Auth::user()->type == 'User3')
                        <li>
                            <a class="nav-link" href="/gate3_visitor/verify">GATE 3</a>
                        </li>
                        @endif
                    </ul>
                </li>
                

                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('logout') }}">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
        <!-- Heading and Logo -->
        <div class="d-flex align-items-center justify-content-center pt-3 pb-2 mb-3 border-bottom">
            <!-- Logo in Main Content -->
            <img src="{{ asset('images/cvsu.png') }}" alt="CvSU Logo" class="logo-main me-2">
            <h1 class="h2">Cavite State University Main</h1>
        </div>
        
        @yield('content')
    </main>
    @endguest

    <script src="{{ asset('js/bootstrap.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
