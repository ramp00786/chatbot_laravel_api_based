<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot Admin - @yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{asset('dist/js-snackbar.css?v=1.4')}}" />
    <link rel="icon" type="image/x-icon" href="{{asset('images/favicon.ico')}}">
    <link rel="stylesheet" href="{{asset('dist/style.css')}}">
    <style>
        .swal2-html-container{
            text-align:left
        }
        input[type="checkbox"][readonly] {
            pointer-events: none;
        }
    </style>
    @stack('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}"> <img src="{{asset('images/bot-icon.png')}}" width="50" alt=""> Chatbot Admin</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            @if(Auth::check())
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ Route::is('admin.dashboard') ? 'active' : '' }} " href="{{ route('admin.dashboard') }}">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Route::is('admin.questions.index') ? 'active' : '' }}" href="{{ route('admin.questions.index') }}">Questions</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Route::is('admin.api-keys') ? 'active' : '' }}" href="{{ route('admin.api-keys') }}">API Keys</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ Route::is('admin.end-inactive-sessions') ? 'active' : '' }}" href="{{ route('admin.end-inactive-sessions') }}">End inactive sessions</a>
                    </li>
                    @if(Auth::user()->email === "pratibhasahu9713@gmail.com")
                    <li class="nav-item">
                        <a class="nav-link {{ Route::is('admin.end-inactive-sessions') ? 'active' : '' }}" href="{{ route('chatbot.import.form') }}">
                            <i class="fas fa-file-import"></i> Import Questions
                        </a>
                    </li>
                    @endif

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Reports
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="{{route('admin.reports.sessions')}}">Session</a></li>
                            {{-- <li><a class="dropdown-item" href="#">Another action</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#">Something else here</a></li> --}}
                        </ul>
                    </li>
                        

                    
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="nav-link btn btn-link">Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
            @endif
            @if (Route::has('login'))
                <ul class="navbar-nav">
                    @auth
                        
                    @else
                        <li class="nav-item">
                            <a href="{{ url('/login') }}" class="nav-link">
                                Login
                            </a>
                        </li>
                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a href="{{ url('/register') }}" class="nav-link">
                                    Register
                                </a>
                            </li>
                        @endif
                    @endauth
                </ul>
            @endif
        </div>
    </nav>

    <div class="container py-4">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        
        @yield('content')
    </div>
    <!-- jQuery 3.7.1 -->
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Extra --}}

    {{-- Extra --}}
    <script src="{{asset('dist/js-snackbar.js?v=1.4')}}"></script>
    <script>
        var colors_Default = function (msg) {
            SnackBar({
                message: msg
            })
        };

        var colors_Success = function (msg) {
            SnackBar({
                message: msg,
                status: "success"
            });
        };

        

        var colors_Error = function (msg) {
            SnackBar({
                message: msg,
                status: "danger"
            })
        }

        var colors_Warning = function (msg) {
            SnackBar({
                message: msg,
                status: "warning"
            })
        }

        var colors_Info = function (msg) {
            SnackBar({
                message: msg,
                status: "info"
            })
        }


        function copyToClipBoard(msg) {
            navigator.clipboard.writeText(msg).then(() => {
                colors_Success('API Key copied to clipboard!');
            }).catch(err => {
                console.error('Failed to copy: ', err);
            });
        }

    </script>
    @stack('scripts')
</body>
</html>