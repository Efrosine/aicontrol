@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-base-content mb-2">Security Alerts</h1>
            <p class="text-base-content/70">Monitor and manage security alerts from your CCTV systems.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Alert Summary -->
            <div class="lg:col-span-2">
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h2 class="card-title">Recent Alerts</h2>
                        
                        <div class="space-y-4">
                            <div class="alert alert-error">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5l-6.928-12c-.77-.833-1.732-.833-2.502 0l-6.928 12c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                                <div>
                                    <h3 class="font-bold">Motion Detected - Camera #1</h3>
                                    <div class="text-xs">Unauthorized access detected in restricted area</div>
                                </div>
                                <div class="text-xs opacity-60">2 min ago</div>
                            </div>

                            <div class="alert alert-warning">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5l-6.928-12c-.77-.833-1.732-.833-2.502 0l-6.928 12c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                                <div>
                                    <h3 class="font-bold">Camera Offline - Camera #5</h3>
                                    <div class="text-xs">Connection lost with parking lot camera</div>
                                </div>
                                <div class="text-xs opacity-60">15 min ago</div>
                            </div>

                            <div class="alert alert-info">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <h3 class="font-bold">System Update</h3>
                                    <div class="text-xs">Security system updated successfully</div>
                                </div>
                                <div class="text-xs opacity-60">1 hour ago</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alert Stats -->
            <div class="space-y-6">
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h3 class="card-title text-lg">Alert Statistics</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span>Critical Alerts</span>
                                <div class="badge badge-error">2</div>
                            </div>
                            <div class="flex justify-between items-center">
                                <span>Warning Alerts</span>
                                <div class="badge badge-warning">5</div>
                            </div>
                            <div class="flex justify-between items-center">
                                <span>Info Alerts</span>
                                <div class="badge badge-info">12</div>
                            </div>
                            <div class="flex justify-between items-center">
                                <span>Resolved Today</span>
                                <div class="badge badge-success">8</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h3 class="card-title text-lg">Quick Actions</h3>
                        <div class="space-y-2">
                            <button class="btn btn-primary btn-sm w-full">Acknowledge All</button>
                            <button class="btn btn-secondary btn-sm w-full">Export Report</button>
                            <button class="btn btn-accent btn-sm w-full">Configure Alerts</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
