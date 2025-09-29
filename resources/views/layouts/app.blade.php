<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="icon" href="{{ asset('image/logo.png') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @yield('styles')
</head>

<body>
    <header class="site-header">
        <div class="logo-container">
            <img src="{{ asset('image/logo.png') }}" alt="Logo Ruang Nifas" class="logo-image">
        </div>
        @if (Session::has('user') && (Request::is('admin/*') || Request::is('superadmin/*')))
            <div class="user-info">
                <svg width="10" height="10" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg"
                    class="user-avatar">
                    <path
                        d="M20.0003 19.9999C24.6027 19.9999 28.3337 16.269 28.3337 11.6666C28.3337 7.06421 24.6027 3.33325 20.0003 3.33325C15.398 3.33325 11.667 7.06421 11.667 11.6666C11.667 16.269 15.398 19.9999 20.0003 19.9999Z"
                        fill="#FFC107" />
                    <path
                        d="M19.9996 24.1667C11.6496 24.1667 4.84961 29.7667 4.84961 36.6667C4.84961 37.1334 5.21628 37.5001 5.68294 37.5001H34.3163C34.7829 37.5001 35.1496 37.1334 35.1496 36.6667C35.1496 29.7667 28.3496 24.1667 19.9996 24.1667Z"
                        fill="#337354" />
                </svg>
                <span class="user-name">
                    {{ Session::get('user')->username ?? '---' }}
                </span>
                <a href="{{ route('logout') }}" class="logout-link" aria-label="Logout">
                    <svg width="30" height="30" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M15 35H8.33333C7.44928 35 6.60143 34.6488 5.97631 34.0237C5.35119 33.3986 5 32.5507 5 31.6667V8.33333C5 7.44928 5.35119 6.60143 5.97631 5.97631C6.60143 5.35119 7.44928 5 8.33333 5H15M26.6667 28.3333L35 20M35 20L26.6667 11.6667M35 20H15"
                            stroke="#DC5E3A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </a>
            </div>
        @endif
    </header>
    <main>
        @yield('content')
    </main>
    <footer class="site-footer">

    </footer>
    <script src="{{ asset('js/script.js') }}"></script>
    @stack('scripts')
</body>

</html>