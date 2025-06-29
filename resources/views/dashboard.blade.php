@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <!-- Dashboard Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-base-content mb-2">Dashboard Overview</h1>
            <p class="text-base-content/70">Welcome back! Here's what's happening in your AI Control Panel.</p>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="stat bg-base-100 shadow-lg rounded-xl border border-base-300">
                <div class="stat-figure text-primary">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div class="stat-title">Total Users</div>
                <div class="stat-value text-primary">{{ \App\Models\User::count() }}</div>
                <div class="stat-desc">System users</div>
            </div>

            <div class="stat bg-base-100 shadow-lg rounded-xl border border-base-300">
                <div class="stat-figure text-secondary">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="stat-title">Active Cameras</div>
                <div class="stat-value text-secondary">{{ \App\Models\Cctv::count() }}</div>
                <div class="stat-desc">CCTV monitoring</div>
            </div>

            <div class="stat bg-base-100 shadow-lg rounded-xl border border-base-300">
                <div class="stat-figure text-accent">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path>
                    </svg>
                </div>
                <div class="stat-title">Scrapper Accounts</div>
                <div class="stat-value text-accent">{{ \App\Models\DummyAccount::count() }}</div>
                <div class="stat-desc">Social media accounts</div>
            </div>

            <div class="stat bg-base-100 shadow-lg rounded-xl border border-base-300">
                <div class="stat-figure text-warning">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5l-6.928-12c-.77-.833-1.732-.833-2.502 0l-6.928 12c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <div class="stat-title">Suspected Accounts</div>
                <div class="stat-value text-warning">{{ \App\Models\SuspectedAccount::count() }}</div>
                <div class="stat-desc">Under monitoring</div>
            </div>
        </div>

        <!-- Main Dashboard Cards -->
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
            <!-- Social Media Management -->
            <div class="card bg-gradient-to-br from-primary to-primary-focus text-primary-content shadow-xl">
                <div class="card-body">
                    <h3 class="card-title text-xl mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path>
                        </svg>
                        Social Media Management
                    </h3>
                    <p class="mb-4 opacity-90">Monitor and analyze social media activity with our AI-powered tools</p>
                    <div class="card-actions justify-end">
                        <a href="{{ route('admin.scraper.form') }}" class="btn btn-sm btn-primary-content">
                            Open Scraper
                        </a>
                        <a href="{{ route('dummy-accounts.index') }}" class="btn btn-sm btn-outline btn-primary-content">
                            Manage Accounts
                        </a>
                    </div>
                </div>
            </div>

            <!-- CCTV Security -->
            <div class="card bg-gradient-to-br from-secondary to-secondary-focus text-secondary-content shadow-xl">
                <div class="card-body">
                    <h3 class="card-title text-xl mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        CCTV Security
                    </h3>
                    <p class="mb-4 opacity-90">Real-time monitoring and AI-powered threat detection</p>
                    <div class="card-actions justify-end">
                        <a href="{{ route('cctv.user-view') }}" class="btn btn-sm btn-secondary-content">
                            Live View
                        </a>
                        <a href="{{ route('cctvs.index') }}" class="btn btn-sm btn-outline btn-secondary-content">
                            Manage Cameras
                        </a>
                    </div>
                </div>
            </div>

            <!-- System Administration -->
            <div class="card bg-gradient-to-br from-accent to-accent-focus text-accent-content shadow-xl">
                <div class="card-body">
                    <h3 class="card-title text-xl mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        System Administration
                    </h3>
                    <p class="mb-4 opacity-90">Manage users, accounts, and system configurations</p>
                    <div class="card-actions justify-end">
                        <a href="{{ route('users.index') }}" class="btn btn-sm btn-accent-content">
                            Manage Users
                        </a>
                        <a href="{{ route('suspected-accounts.index') }}" class="btn btn-sm btn-outline btn-accent-content">
                            Suspected Accounts
                        </a>
                    </div>
                </div>
            </div>

            <!-- Analysis Results -->
            <div class="card bg-gradient-to-br from-info to-info-focus text-info-content shadow-xl">
                <div class="card-body">
                    <h3 class="card-title text-xl mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Analysis Results
                    </h3>
                    <p class="mb-4 opacity-90">View comprehensive reports and analytics data</p>
                    <div class="card-actions justify-end">
                        <a href="{{ route('admin.social_detection_results.index') }}" class="btn btn-sm btn-info-content">
                            Social Analysis
                        </a>
                        <a href="{{ route('scraper.results.list') }}" class="btn btn-sm btn-outline btn-info-content">
                            Scraped Data
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="card bg-base-100 shadow-xl border border-base-300 lg:col-span-2">
                <div class="card-body">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="card-title text-xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Recent Activity
                        </h3>
                        <a href="{{ route('dashboard.activities') }}" class="btn btn-sm btn-outline">View All</a>
                    </div>
                    
                    @if($recentActivities && $recentActivities->count() > 0)
                        <div class="space-y-3">
                            @foreach($recentActivities as $activity)
                                <div class="flex items-center space-x-3 p-3 bg-base-200 rounded-lg hover:bg-base-300 transition-colors">
                                    <div class="badge {{ $activity->badge_class }}">{{ $activity->badge_text }}</div>
                                    <div class="flex-1">
                                        <div class="font-medium text-sm">{{ $activity->title }}</div>
                                        <div class="text-xs opacity-70">{{ $activity->description }}</div>
                                        @if($activity->user)
                                            <div class="text-xs opacity-50 mt-1">by {{ $activity->user->username }}</div>
                                        @endif
                                    </div>
                                    <span class="text-sm opacity-60">{{ $activity->time_ago }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="text-base-content/50 mb-2">
                                <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <p class="text-base-content/70">No recent activities</p>
                            <p class="text-xs text-base-content/50">Activity will appear here as you use the system</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection