<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot Admin - @yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="dist/js-snackbar.css?v=1.4" />
    <style>
        .swal2-html-container{
            text-align:left
        }
    </style>
    @stack('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">Chatbot Admin</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            @if(Auth::check())
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.dashboard') }}">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.questions.index') }}">Questions</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.api-keys') }}">API Keys</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.end-inactive-sessions') }}">End inactive sessions</a>
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
                        <li class="nav-item">
                            <a href="{{ url('/dashboard') }}" class="nav-link">
                                Dashboard
                            </a>
                        </li>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="dist/js-snackbar.js?v=1.4"></script>
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