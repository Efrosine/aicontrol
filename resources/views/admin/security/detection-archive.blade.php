@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Detection Archive</h1>
                <p class="text-gray-600">Browse and manage recorded detection files from security cameras</p>
            </div>
            <div class="flex items-center space-x-3">
                <div class="badge badge-info">{{ count($detectionFiles) }} files found</div>
                <button class="btn btn-outline btn-sm" onclick="refreshArchive()">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Refresh
                </button>
            </div>
        </div>
    </div>

    <!-- Filters and Controls -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" action="{{ route('admin.security.detection-archive') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Camera Filter -->
            <div class="form-control">
                <label class="label">
                    <span class="label-text font-medium">Camera</span>
                </label>
                <select name="camera" class="select select-bordered w-full" onchange="this.form.submit()">
                    <option value="all" {{ $selectedCamera == 'all' || !$selectedCamera ? 'selected' : '' }}>Show All Cameras</option>
                    @foreach($cameras as $camera)
                        <option value="{{ $camera->name }}" {{ $selectedCamera == $camera->name ? 'selected' : '' }}>
                            {{ $camera->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Date Filter -->
            <div class="form-control">
                <label class="label">
                    <span class="label-text font-medium">Date</span>
                </label>
                <input type="date" name="date" value="{{ $selectedDate }}" class="input input-bordered w-full" onchange="this.form.submit()">
            </div>

            <!-- Detection Type Filter -->
            <div class="form-control">
                <label class="label">
                    <span class="label-text font-medium">Detection Type</span>
                </label>
                <select class="select select-bordered w-full">
                    <option>All Types</option>
                    <option>Person</option>
                    <option>Vehicle</option>
                    <option>Motion</option>
                    <option>Face</option>
                    <option>Package</option>
                </select>
            </div>

            <!-- Time Range -->
            <div class="form-control">
                <label class="label">
                    <span class="label-text font-medium">Time Range</span>
                </label>
                <select class="select select-bordered w-full">
                    <option>All Day</option>
                    <option>Morning (06:00-12:00)</option>
                    <option>Afternoon (12:00-18:00)</option>
                    <option>Evening (18:00-24:00)</option>
                    <option>Night (00:00-06:00)</option>
                </select>
            </div>
        </form>
    </div>

    <!-- Storage Configuration -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Storage Configuration</h3>
                <p class="text-sm text-gray-600">Configure MinIO storage settings for detection archive</p>
            </div>
            <div class="flex space-x-2">
                <button 
                    id="test-storage-btn" 
                    class="btn btn-outline btn-sm"
                    onclick="testStorageConnection()"
                >
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Test Connection
                </button>
            </div>
        </div>

        <!-- Storage Status -->
        <div class="mb-6 p-4 rounded-lg {{ $storageStatus['status'] === 'online' ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200' }}">
            <div class="flex items-center">
                <div class="w-3 h-3 rounded-full mr-3 {{ $storageStatus['status'] === 'online' ? 'bg-green-500' : 'bg-red-500' }}"></div>
                <div>
                    <h4 class="font-medium {{ $storageStatus['status'] === 'online' ? 'text-green-800' : 'text-red-800' }}">
                        Storage Status: {{ ucfirst($storageStatus['status']) }}
                    </h4>
                    @if($storageStatus['status'] === 'online')
                        <p class="text-sm text-green-600">
                            Connected to {{ $storageSettings['endpoint'] }}
                            @if(isset($storageStatus['response_time']))
                                ({{ number_format($storageStatus['response_time'] * 1000, 0) }}ms)
                            @endif
                        </p>
                    @else
                        <p class="text-sm text-red-600">
                            Cannot connect to {{ $storageSettings['endpoint'] }}
                            @if(isset($storageStatus['error']))
                                - {{ $storageStatus['error'] }}
                            @endif
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Storage Settings Form -->
        <form id="storage-settings-form" method="POST" action="{{ route('admin.storage.settings.update') }}">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- MinIO Endpoint -->
                <div class="md:col-span-2">
                    <label for="endpoint" class="block text-sm font-medium text-gray-700 mb-2">
                        MinIO Endpoint <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="endpoint" 
                        name="endpoint" 
                        value="{{ old('endpoint', $storageSettings['endpoint']) }}"
                        class="input input-bordered w-full"
                        placeholder="localhost:9000"
                        required
                    >
                    <p class="mt-1 text-sm text-gray-500">
                        The MinIO server endpoint (host:port)
                    </p>
                </div>

                <!-- Access Key -->
                <div>
                    <label for="access_key" class="block text-sm font-medium text-gray-700 mb-2">
                        Access Key <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="access_key" 
                        name="access_key" 
                        value="{{ old('access_key', $storageSettings['access_key']) }}"
                        class="input input-bordered w-full"
                        placeholder="minioadmin"
                        required
                    >
                    <p class="mt-1 text-sm text-gray-500">
                        MinIO access key (username)
                    </p>
                </div>

                <!-- Secret Key -->
                <div>
                    <label for="secret_key" class="block text-sm font-medium text-gray-700 mb-2">
                        Secret Key <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="password" 
                        id="secret_key" 
                        name="secret_key" 
                        value="{{ old('secret_key', $storageSettings['secret_key']) }}"
                        class="input input-bordered w-full"
                        placeholder="••••••••••"
                        required
                    >
                    <p class="mt-1 text-sm text-gray-500">
                        MinIO secret key (password)
                    </p>
                </div>

                <!-- Bucket Name -->
                <div>
                    <label for="bucket" class="block text-sm font-medium text-gray-700 mb-2">
                        Bucket Name <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="bucket" 
                        name="bucket" 
                        value="{{ old('bucket', $storageSettings['bucket']) }}"
                        class="input input-bordered w-full"
                        placeholder="detection-archive"
                        required
                    >
                    <p class="mt-1 text-sm text-gray-500">
                        MinIO bucket name for storing detection files
                    </p>
                </div>

                <!-- Timeout -->
                <div>
                    <label for="timeout" class="block text-sm font-medium text-gray-700 mb-2">
                        Timeout (seconds) <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="number" 
                        id="timeout" 
                        name="timeout" 
                        value="{{ old('timeout', $storageSettings['timeout']) }}"
                        min="5" 
                        max="300"
                        class="input input-bordered w-full"
                        required
                    >
                    <p class="mt-1 text-sm text-gray-500">
                        Connection timeout (5-300 seconds)
                    </p>
                </div>

                <!-- Retry Attempts -->
                <div>
                    <label for="retry_attempts" class="block text-sm font-medium text-gray-700 mb-2">
                        Retry Attempts <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="number" 
                        id="retry_attempts" 
                        name="retry_attempts" 
                        value="{{ old('retry_attempts', $storageSettings['retry_attempts']) }}"
                        min="1" 
                        max="10"
                        class="input input-bordered w-full"
                        required
                    >
                    <p class="mt-1 text-sm text-gray-500">
                        Number of retry attempts (1-10)
                    </p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-4 mt-6">
                <button 
                    type="button" 
                    class="btn btn-outline"
                    onclick="resetStorageForm()"
                >
                    Reset
                </button>
                <button 
                    type="submit" 
                    class="btn btn-primary"
                >
                    Save Storage Settings
                </button>
            </div>
        </form>
    </div>

    <!-- Detection Files Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-900">Detection Files</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="table table-zebra w-full">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="text-left">Type</th>
                        <th class="text-left">File Name</th>
                        <th class="text-left">Camera</th>
                        <th class="text-left">Detection</th>
                        <th class="text-left">Time</th>
                        <th class="text-left">Size</th>
                        <th class="text-left">Confidence</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($detectionFiles as $file)
                        <tr class="hover:bg-gray-50">
                            <td>
                                <div class="flex items-center">
                                    @if($file['type'] == 'video')
                                        <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                        </svg>
                                    @else
                                        <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    @endif
                                    <span class="ml-2 text-xs uppercase tracking-wide text-gray-500">{{ $file['type'] }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="font-mono text-sm">{{ $file['filename'] }}</div>
                            </td>
                            <td>
                                <div class="badge badge-outline">{{ $file['camera'] }}</div>
                            </td>
                            <td>
                                <div class="badge badge-secondary">{{ $file['detection_type'] }}</div>
                            </td>
                            <td>
                                <div class="text-sm">
                                    <div class="font-medium">{{ $file['time_label'] }}</div>
                                    <div class="text-gray-500">{{ date('M j, Y', $file['timestamp']) }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="text-sm text-gray-600">{{ $file['size'] }}</div>
                            </td>
                            <td>
                                <div class="flex items-center">
                                    <div class="w-full bg-gray-200 rounded-full h-2 mr-2">
                                        <div class="bg-green-600 h-2 rounded-full" style="width: {{ $file['confidence'] }}%"></div>
                                    </div>
                                    <span class="text-xs text-gray-600">{{ $file['confidence'] }}%</span>
                                </div>
                            </td>
                            <td>
                                <div class="flex justify-center space-x-2">
                                    <button class="btn btn-sm btn-outline btn-primary" onclick="previewFile('{{ $file['id'] }}', '{{ $file['type'] }}', '{{ $file['filename'] }}')">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        View
                                    </button>
                                    <button class="btn btn-sm btn-outline btn-success" onclick="downloadFile('{{ $file['id'] }}', '{{ $file['filename'] }}')">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        Download
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-12">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2h4a1 1 0 110 2h-1v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6H3a1 1 0 110-2h4zM6 6v12h12V6H6zm4 3a1 1 0 112 0v4a1 1 0 11-2 0v-4zm4 0a1 1 0 112 0v4a1 1 0 11-2 0v-4z"></path>
                                    </svg>
                                    <h3 class="text-lg font-medium text-gray-900 mb-1">No detection files found</h3>
                                    <p class="text-gray-500">Try adjusting your filters or select a different date.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<dialog id="previewModal" class="modal">
    <div class="modal-box w-11/12 max-w-4xl">
        <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
        </form>
        <h3 class="font-bold text-lg mb-4" id="previewTitle">File Preview</h3>
        <div id="previewContent" class="flex justify-center items-center min-h-96 bg-gray-100 rounded-lg">
            <div class="text-gray-500">Loading preview...</div>
        </div>
        <div class="modal-action">
            <button class="btn btn-primary" onclick="downloadCurrentFile()">Download</button>
            <form method="dialog">
                <button class="btn">Close</button>
            </form>
        </div>
    </div>
</dialog>

<script>
let currentFileId = null;
let currentFileName = null;

function previewFile(fileId, type, filename) {
    currentFileId = fileId;
    currentFileName = filename;
    
    document.getElementById('previewTitle').textContent = `Preview: ${filename}`;
    document.getElementById('previewModal').showModal();
    
    // Simulate loading
    const previewContent = document.getElementById('previewContent');
    previewContent.innerHTML = '<div class="loading loading-spinner loading-lg"></div>';
    
    // Mock preview content after short delay
    setTimeout(() => {
        if (type === 'video') {
            previewContent.innerHTML = `
                <div class="w-full">
                    <div class="bg-black rounded-lg flex items-center justify-center h-96">
                        <div class="text-center text-white">
                            <svg class="w-16 h-16 mx-auto mb-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z"/>
                            </svg>
                            <p class="text-lg font-medium">Mock Video Preview</p>
                            <p class="text-sm opacity-75">${filename}</p>
                            <button class="btn btn-primary btn-sm mt-2">Play Video</button>
                        </div>
                    </div>
                </div>`;
        } else {
            previewContent.innerHTML = `
                <div class="w-full">
                    <div class="bg-gray-200 rounded-lg flex items-center justify-center h-96">
                        <div class="text-center text-gray-600">
                            <svg class="w-16 h-16 mx-auto mb-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
                            </svg>
                            <p class="text-lg font-medium">Mock Image Preview</p>
                            <p class="text-sm opacity-75">${filename}</p>
                        </div>
                    </div>
                </div>`;
        }
    }, 1000);
    
    // Mock API call for preview
    fetch(`{{ route('admin.security.detection-archive.preview') }}?file_id=${fileId}`)
        .then(response => response.json())
        .then(data => {
            console.log('Preview loaded:', data);
        })
        .catch(error => {
            console.error('Preview error:', error);
        });
}

function downloadFile(fileId, filename) {
    // Mock download functionality
    console.log(`Downloading file: ${filename} (ID: ${fileId})`);
    
    // Show download notification
    const toast = document.createElement('div');
    toast.className = 'toast toast-top toast-end';
    toast.innerHTML = `
        <div class="alert alert-success">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span>Mock download started for ${filename}</span>
        </div>
    `;
    document.body.appendChild(toast);
    
    // Remove toast after 3 seconds
    setTimeout(() => {
        document.body.removeChild(toast);
    }, 3000);
    
    // Mock API call for download
    fetch(`{{ route('admin.security.detection-archive.download') }}?file_id=${fileId}`)
        .then(response => response.json())
        .then(data => {
            console.log('Download initiated:', data);
        })
        .catch(error => {
            console.error('Download error:', error);
        });
}

function downloadCurrentFile() {
    if (currentFileId && currentFileName) {
        downloadFile(currentFileId, currentFileName);
    }
}

function refreshArchive() {
    location.reload();
}

// Storage configuration functions
function testStorageConnection() {
    const btn = document.getElementById('test-storage-btn');
    
    // Show loading state
    btn.disabled = true;
    btn.innerHTML = `
        <svg class="w-4 h-4 mr-1 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
        </svg>
        Testing...
    `;
    
    // Make request
    fetch('{{ route("admin.storage.settings.test") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        showStorageTestResult(data);
    })
    .catch(error => {
        showStorageTestResult({
            status: 'offline',
            error: error.message
        });
    })
    .finally(() => {
        // Reset button
        btn.disabled = false;
        btn.innerHTML = `
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Test Connection
        `;
    });
}

function showStorageTestResult(data) {
    const toast = document.createElement('div');
    toast.className = 'toast toast-top toast-end';
    
    if (data.status === 'online') {
        toast.innerHTML = `
            <div class="alert alert-success">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span>Storage connection successful! ${data.response_time ? `(${Math.round(data.response_time * 1000)}ms)` : ''}</span>
            </div>
        `;
    } else {
        toast.innerHTML = `
            <div class="alert alert-error">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                <span>Storage connection failed${data.error ? ': ' + data.error : ''}</span>
            </div>
        `;
    }
    
    document.body.appendChild(toast);
    
    // Remove toast after 5 seconds
    setTimeout(() => {
        if (document.body.contains(toast)) {
            document.body.removeChild(toast);
        }
    }, 5000);
}

function resetStorageForm() {
    document.getElementById('storage-settings-form').reset();
}

// Handle storage settings form submission
document.getElementById('storage-settings-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    
    // Show loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML = 'Saving...';
    
    fetch(this.action, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(Object.fromEntries(formData))
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showStorageTestResult({
                status: 'online',
                message: data.message
            });
            // Reload page to update status
            setTimeout(() => location.reload(), 2000);
        } else {
            showStorageTestResult({
                status: 'offline',
                error: data.message
            });
        }
    })
    .catch(error => {
        showStorageTestResult({
            status: 'offline',
            error: 'Failed to save settings: ' + error.message
        });
    })
    .finally(() => {
        // Reset button
        submitBtn.disabled = false;
        submitBtn.innerHTML = 'Save Storage Settings';
    });
});

// Auto-refresh every 30 seconds (optional)
// setInterval(refreshArchive, 30000);
</script>
@endsection
