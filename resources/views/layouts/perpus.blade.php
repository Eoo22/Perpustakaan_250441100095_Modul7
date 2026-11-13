<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Perpustakaan Digital</title>
    <link rel="stylesheet" href="{{ asset('css/perpus.css') }}">
    <style>
        .navbar .nav-right { margin-left: auto; display: flex; align-items: center; gap: 10px; color: #ffffff; }
        .navbar .nav-right form { margin: 0; }
        .navbar .nav-right button {
            background: none; border: 0; color: #ffffff; cursor: pointer;
            font-weight: bold; font-size: 16px; padding: 5px 10px; border-radius: 4px;
        }
        .navbar .nav-right button:hover { background-color: #2c3e50; }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <h1>Perpustakaan Digital</h1>
            <p class="nim">NIM: 250441100095</p>
        </div>
    </header>

    <nav class="navbar">
        <div class="container">
            <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Beranda</a>
            <a href="{{ route('buku.index') }}" class="{{ request()->routeIs('buku.*') ? 'active' : '' }}">Daftar Buku</a>

            <div class="nav-right">
                @guest
                    <a href="{{ route('login') }}" class="{{ request()->routeIs('login') ? 'active' : '' }}">Login</a>
                    <a href="{{ route('register') }}" class="{{ request()->routeIs('register') ? 'active' : '' }}">Daftar</a>
                @endguest

                @auth
                    <span>{{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit">Logout</button>
                    </form>
                @endauth
            </div>
        </div>
    </nav>

    <main class="container main-content">
        @yield('content')
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2026 Perpustakaan Digital. Tugas Praktikum Laravel.</p>
        </div>
    </footer>
</body>
</html>
