<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title')</title>
    <script>
        (function () {
            const getStoredTheme = () => localStorage.getItem('theme');
            const getPreferredTheme = () => {
                const storedTheme = getStoredTheme();
                if (storedTheme) {
                    return storedTheme;
                }
                return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            };
            const theme = getPreferredTheme();
            if (theme === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                document.documentElement.setAttribute('data-bs-theme', 'dark');
            } else {
                document.documentElement.setAttribute('data-bs-theme', theme);
            }
        })();
    </script>

    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    @stack('styles')

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <style>
        .form-control[type=number]::-webkit-outer-spin-button,
        .form-control[type=number]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .form-control[type=number] {
            -moz-appearance: textfield;
        }
    </style>

</head>

<body class="d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="/">
                GiveHope
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto">

                </ul>

                <ul class="navbar-nav ms-auto">
                    @guest
                        @if (Route::has('login'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                        @endif

                    @else
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Auth::user()->name }}
                            </a>

                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                @if (Auth::user()->is_admin)
                                    <a href="{{ route('admin.dashboard') }}" class="dropdown-item">
                                        <i class="fa-solid fa-screwdriver-wrench"></i> Painel de Administração
                                    </a>

                                @else
                                    <a href="{{ route('painel.dashboard') }}" class="dropdown-item">
                                        <i class="fa-solid fa-church"></i> Painel da Paróquia
                                    </a>
                                @endif

                                <div class="dropdown-divider"></div>

                                <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                                                             document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    @endguest
                </ul>
                <ul class="navbar-nav ms-2">
                    <li class="nav-item">
                        <button class="btn nav-link px-2" onclick="toggleTheme()" aria-label="Toggle theme">
                            <i class="fa-solid fa-sun theme-icon-light d-none"></i>
                            <i class="fa-solid fa-moon theme-icon-dark d-none"></i>
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container flex-grow-1 py-4">
        @yield('content')
    </div>
    <footer class="py-3 my-4 mt-5">
        <ul class="nav justify-content-center border-bottom pb-3 mb-3">
            <li class="nav-item"><a href="/" class="nav-link px-2 text-body-secondary">Início</a>
            </li>
            <li class="nav-item"><a href="/soon" class="nav-link px-2 text-body-secondary">Em Breve</a>
            </li>
            <li class="nav-item"><a href="/inprogress" class="nav-link px-2 text-body-secondary">Em Andamento</a>
            </li>
            <li class="nav-item"><a href="/finished" class="nav-link px-2 text-body-secondary">Finalizados</a>
            </li>
            <li class="nav-item"><a href="/about" class="nav-link px-2 text-body-secondary">Sobre</a>
            </li>
        </ul>
        <p class="text-center text-body-secondary">© 2025 GiveHope</p>
    </footer>
    @stack('scripts')
</body>

</html>