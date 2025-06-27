<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'AI Control Panel') }}</title>

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="min-h-screen bg-base-200">
        <!-- Navbar -->
        @auth
        <div class="navbar bg-base-100 shadow-md">
            <div class="flex-1">
                <a href="{{ auth()->user()->role === 'admin' ? route('dashboard') : route('home') }}" class="btn btn-ghost text-xl">AI Control Panel</a>
            </div>
            <div class="flex-none">
                <div class="dropdown dropdown-end">
                    <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar">
                        <div class="w-10 rounded-full bg-primary text-primary-content flex items-center justify-center">
                            <span class="text-xl">{{ substr(auth()->user()->username, 0, 1) }}</span>
                        </div>
                    </div>
                    <ul tabindex="0" class="menu dropdown-content mt-3 z-[1] p-2 shadow bg-base-100 rounded-box w-52">
                        <li class="menu-title">{{ auth()->user()->username }}</li>
                        <li class="menu-title text-xs opacity-50">{{ auth()->user()->role }}</li>
                        @if(auth()->user()->role === 'admin')
                        <li><a href="{{ route('users.index') }}">Manage Users</a></li>
                        @endif
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left hover:bg-base-200 rounded-lg py-2 px-4">Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        @endauth

        <!-- Page Content -->
        <main>
            @yield('content')
        </main>
    </div>
</body>
</html>
