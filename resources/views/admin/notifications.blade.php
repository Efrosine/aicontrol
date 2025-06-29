@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-base-content mb-2">Notifications</h1>
            <p class="text-base-content/70">Manage your notification preferences and view recent alerts.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Notifications List -->
            <div class="lg:col-span-2">
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="card-title">Recent Notifications</h2>
                            <button class="btn btn-sm btn-ghost">Mark all as read</button>
                        </div>
                        
                        <div class="space-y-4">
                            <div class="flex items-start space-x-4 p-4 bg-error/10 border-l-4 border-error rounded-r-lg">
                                <div class="w-2 h-2 bg-error rounded-full mt-2"></div>
                                <div class="flex-1">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="font-semibold text-error">Security Alert</h4>
                                            <p class="text-sm opacity-80">Unauthorized access detected in restricted area - Camera #1</p>
                                        </div>
                                        <span class="text-xs opacity-60">2 min ago</span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-start space-x-4 p-4 bg-warning/10 border-l-4 border-warning rounded-r-lg">
                                <div class="w-2 h-2 bg-warning rounded-full mt-2"></div>
                                <div class="flex-1">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="font-semibold text-warning">System Warning</h4>
                                            <p class="text-sm opacity-80">Camera offline - Parking lot surveillance camera #5</p>
                                        </div>
                                        <span class="text-xs opacity-60">15 min ago</span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-start space-x-4 p-4 bg-info/10 border-l-4 border-info rounded-r-lg">
                                <div class="w-2 h-2 bg-info rounded-full mt-2"></div>
                                <div class="flex-1">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="font-semibold text-info">Social Media Update</h4>
                                            <p class="text-sm opacity-80">New scraped data available from Instagram monitoring</p>
                                        </div>
                                        <span class="text-xs opacity-60">30 min ago</span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-start space-x-4 p-4 bg-success/10 border-l-4 border-success rounded-r-lg">
                                <div class="w-2 h-2 bg-success rounded-full mt-2"></div>
                                <div class="flex-1">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="font-semibold text-success">System Update</h4>
                                            <p class="text-sm opacity-80">AI analysis engine updated successfully</p>
                                        </div>
                                        <span class="text-xs opacity-60">1 hour ago</span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-start space-x-4 p-4 bg-base-200 border-l-4 border-base-300 rounded-r-lg opacity-60">
                                <div class="w-2 h-2 bg-base-400 rounded-full mt-2"></div>
                                <div class="flex-1">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="font-semibold">Daily Report</h4>
                                            <p class="text-sm opacity-80">Daily security summary report generated</p>
                                        </div>
                                        <span class="text-xs opacity-60">3 hours ago</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="divider"></div>
                        <div class="text-center">
                            <button class="btn btn-outline btn-sm">Load More Notifications</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notification Settings -->
            <div class="space-y-6">
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h3 class="card-title text-lg">Notification Settings</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <h4 class="font-semibold mb-2">Security Alerts</h4>
                                <div class="form-control">
                                    <label class="label cursor-pointer">
                                        <span class="label-text">Email notifications</span>
                                        <input type="checkbox" checked="checked" class="checkbox checkbox-primary" />
                                    </label>
                                </div>
                                <div class="form-control">
                                    <label class="label cursor-pointer">
                                        <span class="label-text">Push notifications</span>
                                        <input type="checkbox" checked="checked" class="checkbox checkbox-primary" />
                                    </label>
                                </div>
                                <div class="form-control">
                                    <label class="label cursor-pointer">
                                        <span class="label-text">SMS alerts</span>
                                        <input type="checkbox" class="checkbox checkbox-primary" />
                                    </label>
                                </div>
                            </div>

                            <div class="divider"></div>

                            <div>
                                <h4 class="font-semibold mb-2">Social Media</h4>
                                <div class="form-control">
                                    <label class="label cursor-pointer">
                                        <span class="label-text">Scraping completed</span>
                                        <input type="checkbox" checked="checked" class="checkbox checkbox-secondary" />
                                    </label>
                                </div>
                                <div class="form-control">
                                    <label class="label cursor-pointer">
                                        <span class="label-text">Analysis results</span>
                                        <input type="checkbox" checked="checked" class="checkbox checkbox-secondary" />
                                    </label>
                                </div>
                            </div>

                            <div class="divider"></div>

                            <div>
                                <h4 class="font-semibold mb-2">System</h4>
                                <div class="form-control">
                                    <label class="label cursor-pointer">
                                        <span class="label-text">System updates</span>
                                        <input type="checkbox" checked="checked" class="checkbox checkbox-accent" />
                                    </label>
                                </div>
                                <div class="form-control">
                                    <label class="label cursor-pointer">
                                        <span class="label-text">Maintenance alerts</span>
                                        <input type="checkbox" checked="checked" class="checkbox checkbox-accent" />
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="card-actions justify-end mt-4">
                            <button class="btn btn-primary btn-sm">Save Settings</button>
                        </div>
                    </div>
                </div>

                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h3 class="card-title text-lg">Statistics</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span>Unread</span>
                                <div class="badge badge-error">3</div>
                            </div>
                            <div class="flex justify-between items-center">
                                <span>Today</span>
                                <div class="badge badge-info">12</div>
                            </div>
                            <div class="flex justify-between items-center">
                                <span>This Week</span>
                                <div class="badge badge-neutral">45</div>
                            </div>
                            <div class="flex justify-between items-center">
                                <span>This Month</span>
                                <div class="badge badge-neutral">156</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
