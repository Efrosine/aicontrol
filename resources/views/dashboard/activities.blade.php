@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <!-- Activities Header -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-base-content mb-2">System Activities</h1>
                    <p class="text-base-content/70">Complete log of all system activities and events</p>
                </div>
                <a href="{{ route('dashboard') }}" class="btn btn-outline">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m0 7h18"></path>
                    </svg>
                    Back to Dashboard
                </a>
            </div>
        </div>

        <!-- Activity Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="stat bg-base-100 shadow-lg rounded-xl border border-base-300">
                <div class="stat-figure text-primary">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div class="stat-title">Total Activities</div>
                <div class="stat-value text-primary">{{ $activities->total() }}</div>
                <div class="stat-desc">All recorded events</div>
            </div>

            <div class="stat bg-base-100 shadow-lg rounded-xl border border-base-300">
                <div class="stat-figure text-success">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <div class="stat-title">Success</div>
                <div class="stat-value text-success">{{ $activities->where('status', 'success')->count() }}</div>
                <div class="stat-desc">Successful operations</div>
            </div>

            <div class="stat bg-base-100 shadow-lg rounded-xl border border-base-300">
                <div class="stat-figure text-warning">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5l-6.928-12c-.77-.833-1.732-.833-2.502 0l-6.928 12c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <div class="stat-title">Warnings</div>
                <div class="stat-value text-warning">{{ $activities->where('status', 'warning')->count() }}</div>
                <div class="stat-desc">Warning events</div>
            </div>

            <div class="stat bg-base-100 shadow-lg rounded-xl border border-base-300">
                <div class="stat-figure text-error">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                <div class="stat-title">Errors</div>
                <div class="stat-value text-error">{{ $activities->where('status', 'error')->count() }}</div>
                <div class="stat-desc">Error events</div>
            </div>
        </div>

        <!-- Activities List -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title text-2xl mb-6">Activity Log</h2>
                
                @if($activities->count() > 0)
                    <div class="space-y-4">
                        @foreach($activities as $activity)
                            <div class="flex items-start space-x-4 p-4 bg-base-200 rounded-lg hover:bg-base-300 transition-colors">
                                <div class="flex-shrink-0">
                                    <div class="badge {{ $activity->badge_class }} badge-lg">{{ $activity->badge_text }}</div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h3 class="font-semibold text-base-content">{{ $activity->title }}</h3>
                                            <p class="text-sm text-base-content/70 mt-1">{{ $activity->description }}</p>
                                            
                                            <div class="flex items-center space-x-4 mt-2 text-xs text-base-content/50">
                                                <span class="flex items-center">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                                    </svg>
                                                    {{ ucfirst($activity->type) }}
                                                </span>
                                                
                                                @if($activity->user)
                                                    <span class="flex items-center">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                        </svg>
                                                        {{ $activity->user->username }}
                                                    </span>
                                                @else
                                                    <span class="flex items-center">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path>
                                                        </svg>
                                                        System
                                                    </span>
                                                @endif
                                                
                                                <span class="flex items-center">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    {{ $activity->occurred_at->format('M j, Y g:i A') }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="text-sm text-base-content/60 ml-4">
                                            {{ $activity->time_ago }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Pagination -->
                    <div class="mt-8">
                        {{ $activities->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="text-base-content/50 mb-4">
                            <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-base-content/70 mb-2">No Activities Found</h3>
                        <p class="text-base-content/50">Activity logs will appear here as you use the system.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
