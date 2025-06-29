@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-base-content mb-2">Zone Management</h1>
            <p class="text-base-content/70">Configure and manage security zones for your CCTV monitoring system.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Zone List -->
            <div class="lg:col-span-3">
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="card-title">Security Zones</h2>
                            <button class="btn btn-primary btn-sm">Add New Zone</button>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Zone Name</th>
                                        <th>Cameras</th>
                                        <th>Status</th>
                                        <th>Last Activity</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="flex items-center space-x-3">
                                                <div class="w-3 h-3 bg-success rounded-full"></div>
                                                <span class="font-medium">Main Entrance</span>
                                            </div>
                                        </td>
                                        <td>3 cameras</td>
                                        <td><div class="badge badge-success">Active</div></td>
                                        <td>2 min ago</td>
                                        <td>
                                            <div class="flex space-x-2">
                                                <button class="btn btn-sm btn-ghost">Edit</button>
                                                <button class="btn btn-sm btn-ghost">View</button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="flex items-center space-x-3">
                                                <div class="w-3 h-3 bg-warning rounded-full"></div>
                                                <span class="font-medium">Parking Lot</span>
                                            </div>
                                        </td>
                                        <td>5 cameras</td>
                                        <td><div class="badge badge-warning">Warning</div></td>
                                        <td>15 min ago</td>
                                        <td>
                                            <div class="flex space-x-2">
                                                <button class="btn btn-sm btn-ghost">Edit</button>
                                                <button class="btn btn-sm btn-ghost">View</button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="flex items-center space-x-3">
                                                <div class="w-3 h-3 bg-error rounded-full"></div>
                                                <span class="font-medium">Restricted Area</span>
                                            </div>
                                        </td>
                                        <td>2 cameras</td>
                                        <td><div class="badge badge-error">Offline</div></td>
                                        <td>1 hour ago</td>
                                        <td>
                                            <div class="flex space-x-2">
                                                <button class="btn btn-sm btn-ghost">Edit</button>
                                                <button class="btn btn-sm btn-ghost">View</button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="flex items-center space-x-3">
                                                <div class="w-3 h-3 bg-success rounded-full"></div>
                                                <span class="font-medium">Office Building</span>
                                            </div>
                                        </td>
                                        <td>8 cameras</td>
                                        <td><div class="badge badge-success">Active</div></td>
                                        <td>5 min ago</td>
                                        <td>
                                            <div class="flex space-x-2">
                                                <button class="btn btn-sm btn-ghost">Edit</button>
                                                <button class="btn btn-sm btn-ghost">View</button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Zone Controls -->
            <div class="space-y-6">
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h3 class="card-title text-lg">Zone Statistics</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span>Total Zones</span>
                                <div class="badge badge-neutral">4</div>
                            </div>
                            <div class="flex justify-between items-center">
                                <span>Active Zones</span>
                                <div class="badge badge-success">2</div>
                            </div>
                            <div class="flex justify-between items-center">
                                <span>Warning Zones</span>
                                <div class="badge badge-warning">1</div>
                            </div>
                            <div class="flex justify-between items-center">
                                <span>Offline Zones</span>
                                <div class="badge badge-error">1</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h3 class="card-title text-lg">Quick Actions</h3>
                        <div class="space-y-2">
                            <button class="btn btn-primary btn-sm w-full">Create Zone</button>
                            <button class="btn btn-secondary btn-sm w-full">Bulk Edit</button>
                            <button class="btn btn-accent btn-sm w-full">Zone Templates</button>
                            <button class="btn btn-info btn-sm w-full">Export Config</button>
                        </div>
                    </div>
                </div>

                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h3 class="card-title text-lg">Zone Settings</h3>
                        <div class="form-control">
                            <label class="label cursor-pointer">
                                <span class="label-text">Auto-detect motion</span>
                                <input type="checkbox" checked="checked" class="checkbox checkbox-primary" />
                            </label>
                        </div>
                        <div class="form-control">
                            <label class="label cursor-pointer">
                                <span class="label-text">Send notifications</span>
                                <input type="checkbox" checked="checked" class="checkbox checkbox-primary" />
                            </label>
                        </div>
                        <div class="form-control">
                            <label class="label cursor-pointer">
                                <span class="label-text">Record events</span>
                                <input type="checkbox" checked="checked" class="checkbox checkbox-primary" />
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
