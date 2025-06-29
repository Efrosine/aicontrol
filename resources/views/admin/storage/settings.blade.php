@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Storage Configuration</h1>
                <p class="text-gray-600">Configure MinIO storage settings for detection archive and file management</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.security.detection-archive') }}" class="btn btn-outline btn-sm">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Archive
                </a>
                <button class="btn btn-outline btn-sm" id="test-storage-btn" onclick="testStorageConnection()">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Test Connection
                </button>
            </div>
        </div>
    </div>

    <!-- Storage Status -->
    <div class="mb-6 p-4 rounded-lg {{ $storageStatus['status'] === 'online' ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200' }}">
        <div class="flex items-center">
            <div class="w-3 h-3 rounded-full mr-3 {{ $storageStatus['status'] === 'online' ? 'bg-green-500' : 'bg-red-500' }}"></div>
            <div>
                <h3 class="font-medium {{ $storageStatus['status'] === 'online' ? 'text-green-800' : 'text-red-800' }}">
                    Storage Status: {{ ucfirst($storageStatus['status']) }}
                </h3>
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
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">MinIO Connection Settings</h3>
            <p class="text-sm text-gray-600">Configure your MinIO server connection and bucket settings</p>
        </div>

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
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('endpoint') border-red-500 @enderror"
                        placeholder="localhost:9000"
                        required
                    >
                    @error('endpoint')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">
                        The MinIO server endpoint in the format host:port (e.g., localhost:9000)
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
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('access_key') border-red-500 @enderror"
                        placeholder="minioadmin"
                        required
                    >
                    @error('access_key')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">
                        MinIO access key (username for authentication)
                    </p>
                </div>

                <!-- Secret Key -->
                <div>
                    <label for="secret_key" class="block text-sm font-medium text-gray-700 mb-2">
                        Secret Key <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            id="secret_key" 
                            name="secret_key" 
                            value="{{ old('secret_key', $storageSettings['secret_key']) }}"
                            class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('secret_key') border-red-500 @enderror"
                            placeholder="••••••••••"
                            required
                        >
                        <button type="button" onclick="togglePasswordVisibility('secret_key')" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                    </div>
                    @error('secret_key')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">
                        MinIO secret key (password for authentication)
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
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('bucket') border-red-500 @enderror"
                        placeholder="detection-archive"
                        required
                    >
                    @error('bucket')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">
                        MinIO bucket name for storing detection files
                    </p>
                </div>

                <!-- Timeout -->
                <div>
                    <label for="timeout" class="block text-sm font-medium text-gray-700 mb-2">
                        Connection Timeout (seconds) <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="number" 
                        id="timeout" 
                        name="timeout" 
                        value="{{ old('timeout', $storageSettings['timeout']) }}"
                        min="5" 
                        max="300"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('timeout') border-red-500 @enderror"
                        required
                    >
                    @error('timeout')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">
                        Maximum time to wait for storage operations (5-300 seconds)
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
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('retry_attempts') border-red-500 @enderror"
                        required
                    >
                    @error('retry_attempts')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">
                        Number of times to retry failed operations (1-10)
                    </p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-4 mt-8">
                <a 
                    href="{{ route('admin.security.detection-archive') }}" 
                    class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition duration-200"
                >
                    Cancel
                </a>
                <button 
                    type="button" 
                    class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition duration-200"
                    onclick="resetStorageForm()"
                >
                    Reset
                </button>
                <button 
                    type="submit" 
                    class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition duration-200"
                >
                    Save Settings
                </button>
            </div>
        </form>
    </div>

    <!-- Storage Information -->
    <div class="mt-6 bg-gray-50 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Storage Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white p-4 rounded-lg border">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Current Bucket</p>
                        <p class="text-sm text-gray-600">{{ $storageSettings['bucket'] }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white p-4 rounded-lg border">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Connection Status</p>
                        <p class="text-sm {{ $storageStatus['status'] === 'online' ? 'text-green-600' : 'text-red-600' }}">
                            {{ ucfirst($storageStatus['status']) }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="bg-white p-4 rounded-lg border">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Timeout</p>
                        <p class="text-sm text-gray-600">{{ $storageSettings['timeout'] }}s</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
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

function togglePasswordVisibility(fieldId) {
    const field = document.getElementById(fieldId);
    const type = field.getAttribute('type') === 'password' ? 'text' : 'password';
    field.setAttribute('type', type);
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
        submitBtn.innerHTML = 'Save Settings';
    });
});
</script>
@endsection
