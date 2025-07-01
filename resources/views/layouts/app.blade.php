<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'AI Control Panel') }}</title>

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('scripts')
</head>

<body>
    <div class="min-h-screen bg-base-200">
        <!-- Top Navigation Bar -->
        @auth
            <div class="navbar bg-base-100 shadow-lg border-b border-base-300">
                <div class="navbar-start">
                    <!-- Logo -->
                    <a href="{{ auth()->user()->role === 'admin' ? route('dashboard') : route('home') }}"
                        class="btn btn-ghost text-xl font-bold text-primary">
                        <svg class="w-8 h-8 mr-2" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.94-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z" />
                        </svg>
                        AI Control Panel
                    </a>
                </div>

                <div class="navbar-center hidden lg:flex">
                    <ul class="menu menu-horizontal px-1 gap-2">
                        <!-- Dashboard Link -->
                        <li>
                            <a href="{{ auth()->user()->role === 'admin' ? route('dashboard') : route('home') }}"
                                class="btn btn-ghost {{ request()->routeIs('dashboard', 'home') ? 'btn-active' : '' }}">
                                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 5a2 2 0 012-2h4a2 2 0 012 2v0M8 5a2 2 0 00-2 2v0h12a2 2 0 00-2-2v0"></path>
                                </svg>
                                Dashboard
                            </a>
                        </li>

                        <!-- Social Media Dropdown -->
                        <li>
                            <details class="dropdown">
                                <summary class="btn btn-ghost">
                                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z">
                                        </path>
                                    </svg>
                                    Social Media
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </summary>
                                <ul class="dropdown-content menu bg-base-100 rounded-box z-[9999] w-52 p-2 shadow-lg border border-base-300"
                                    style="z-index: 9999 !important;">
                                    @if(auth()->user()->role === 'admin')
                                        <li><a href="{{ route('admin.scraper.form') }}" class="flex items-center">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                                </svg>
                                                Data Scraper
                                            </a></li>
                                        <li><a href="{{ route('dummy-accounts.index') }}" class="flex items-center">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                                    </path>
                                                </svg>
                                                Accounts for Scraper
                                            </a></li>
                                        <li><a href="{{ route('suspected-accounts.index') }}" class="flex items-center">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5l-6.928-12c-.77-.833-1.732-.833-2.502 0l-6.928 12c-.77.833.192 2.5 1.732 2.5z">
                                                    </path>
                                                </svg>
                                                Suspected Accounts
                                            </a></li>
                                        <li><a href="{{ route('admin.social_detection_results.index') }}"
                                                class="flex items-center">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                                    </path>
                                                </svg>
                                                Analysis Results
                                            </a></li>
                                    @endif
                                    <li><a href="{{ route('scraper.results.list') }}" class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                                            </svg>
                                            Scraped Data
                                        </a></li>
                                </ul>
                            </details>
                        </li>

                        <!-- Security Dropdown -->
                        <li>
                            <details class="dropdown">
                                <summary class="btn btn-ghost">
                                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    Security
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </summary>
                                <ul class="dropdown-content menu bg-base-100 rounded-box z-[9999] w-52 p-2 shadow-lg border border-base-300"
                                    style="z-index: 9999 !important;">
                                    @if(auth()->user()->role === 'admin')
                                        <li><a href="{{ route('cctvs.index') }}" class="flex items-center">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z">
                                                    </path>
                                                </svg>
                                                Camera Management
                                            </a></li>
                                    @endif
                                    <li><a href="{{ route('cctv.user-view') }}" class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h8m-5-10v20m5-20v20">
                                                </path>
                                            </svg>
                                            Live Camera View
                                        </a></li>
                                    <li><a href="{{ route('admin.security.alerts') }}" class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5l-6.928-12c-.77-.833-1.732-.833-2.502 0l-6.928 12c-.77.833.192 2.5 1.732 2.5z">
                                                </path>
                                            </svg>
                                            Security Alerts
                                        </a></li>
                                    <li><a href="{{ route('admin.security.zones') }}" class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 4m0 13V4m-6 3l6-3">
                                                </path>
                                            </svg>
                                            Zone Management
                                        </a></li>
                                    <li><a href="{{ route('admin.security.detection-archive') }}" class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4">
                                                </path>
                                            </svg>
                                            Detection Archive
                                        </a></li>
                                </ul>
                            </details>
                        </li>

                        @if(auth()->user()->role === 'admin')
                            <!-- Settings Link -->
                            <li>
                                <a href="{{ route('users.index') }}"
                                    class="btn btn-ghost {{ request()->routeIs('users.*') ? 'btn-active' : '' }}">
                                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                                        </path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    Settings
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>

                <div class="navbar-end">
                    <!-- Notifications -->
                    <div class="dropdown dropdown-end mr-2">
                        <div tabindex="0" role="button" class="btn btn-ghost btn-circle">
                            <div class="indicator">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                                    </path>
                                </svg>
                                <span class="badge badge-xs badge-primary indicator-item">3</span>
                            </div>
                        </div>
                        <div tabindex="0" class="dropdown-content card card-compact w-64 bg-base-100 shadow-lg z-[9999]"
                            style="z-index: 9999 !important;">
                            <div class="card-body">
                                <h3 class="card-title text-sm">Notifications</h3>
                                <div class="space-y-2">
                                    <div class="alert alert-info py-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span class="text-xs">New scraped data available</span>
                                    </div>
                                    <div class="alert alert-warning py-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5l-6.928-12c-.77-.833-1.732-.833-2.502 0l-6.928 12c-.77.833.192 2.5 1.732 2.5z">
                                            </path>
                                        </svg>
                                        <span class="text-xs">Security alert detected</span>
                                    </div>
                                    <div class="alert alert-success py-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <span class="text-xs">System update completed</span>
                                    </div>
                                </div>
                                <div class="card-actions">
                                    <a href="{{ route('admin.notifications') }}" class="btn btn-sm btn-block">View All</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- User Profile Dropdown -->
                    <div class="dropdown dropdown-end">
                        <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar">
                            <div class="w-10 rounded-full bg-primary text-primary-content flex items-center justify-center">
                                <span class="text-xl font-semibold">{{ substr(auth()->user()->username, 0, 1) }}</span>
                            </div>
                        </div>
                        <ul tabindex="0"
                            class="menu dropdown-content mt-3 z-[9999] p-2 shadow-lg bg-base-100 rounded-box w-52 border border-base-300"
                            style="z-index: 9999 !important;">
                            <li class="menu-title">
                                <span class="font-semibold">{{ auth()->user()->username }}</span>
                            </li>
                            <li class="menu-title text-xs opacity-50 mb-2">
                                <span class="badge badge-primary badge-sm">{{ ucfirst(auth()->user()->role) }}</span>
                            </li>
                            <div class="divider my-1"></div>
                            <li><a href="#" class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Profile Settings
                                </a></li>
                            <li><a href="{{ route('admin.notifications') }}" class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                                        </path>
                                    </svg>
                                    Notifications
                                </a></li>
                            <div class="divider my-1"></div>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left flex items-center text-error">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                            </path>
                                        </svg>
                                        Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Mobile Menu Button -->
                <div class="navbar-end lg:hidden">
                    <div class="dropdown dropdown-end">
                        <div tabindex="0" role="button" class="btn btn-ghost">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </div>
                        <ul tabindex="0" class="menu dropdown-content mt-3 z-[9999] p-2 shadow bg-base-100 rounded-box w-52"
                            style="z-index: 9999 !important;">
                            <li><a
                                    href="{{ auth()->user()->role === 'admin' ? route('dashboard') : route('home') }}">Dashboard</a>
                            </li>
                            <li><a>Social Media</a></li>
                            <li><a>Security</a></li>
                            @if(auth()->user()->role === 'admin')
                                <li><a href="{{ route('users.index') }}">Settings</a></li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        @endauth
        <style>
            /* Fix dropdown z-index issues */
            .dropdown-content,
            .dropdown .dropdown-content {
                z-index: 9999 !important;
                position: relative;
            }

            /* Ensure dropdown menus appear above all content */
            .navbar .dropdown-content {
                z-index: 9999 !important;
            }

            /* Fix for DaisyUI dropdown positioning */
            .dropdown:hover .dropdown-content,
            .dropdown.dropdown-open .dropdown-content,
            .dropdown:focus .dropdown-content,
            .dropdown:focus-within .dropdown-content {
                z-index: 9999 !important;
            }

            /* Ensure proper stacking context */
            .navbar {
                z-index: 1000;
                position: relative;
            }

            /* Make sure dropdown content is properly positioned */
            details[open]>summary~* {
                z-index: 9999 !important;
            }
        </style>
        <!-- Page Content -->
        <main>
            @yield('content')
        </main>
    </div>
</body>

</html>