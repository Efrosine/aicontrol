@extends('layouts.app')

@section('title', 'CCTV Camera Details')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center">
            <a href="{{ route('cctvs.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">CCTV Camera Details</h1>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('cctvs.edit', $camera['id']) }}" 
               class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit Camera
            </a>
        </div>
    </div>

    @if (session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
            <div class="flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                {{ session('error') }}
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Camera Information -->
        <div class="bg-gray-50 rounded-lg p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Camera Information
            </h2>

            <div class="space-y-4">
                <div class="flex justify-between items-start">
                    <span class="text-sm font-medium text-gray-500">Name</span>
                    <span class="text-sm text-gray-900 text-right">{{ $camera['name'] ?? 'Unknown' }}</span>
                </div>
                <div class="flex justify-between items-start">
                    <span class="text-sm font-medium text-gray-500">Location</span>
                    <span class="text-sm text-gray-900 text-right">{{ $camera['location'] ?? 'Unknown' }}</span>
                </div>
                <div class="flex justify-between items-start">
                    <span class="text-sm font-medium text-gray-500">IP Address</span>
                    <span class="text-sm text-gray-900 text-right font-mono">{{ $camera['ip_address'] ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between items-start">
                    <span class="text-sm font-medium text-gray-500">Port</span>
                    <span class="text-sm text-gray-900 text-right">{{ $camera['port'] ?? 'N/A' }}</span>
                </div>
                @if(isset($camera['username']) && $camera['username'])
                <div class="flex justify-between items-start">
                    <span class="text-sm font-medium text-gray-500">Username</span>
                    <span class="text-sm text-gray-900 text-right">{{ $camera['username'] }}</span>
                </div>
                @endif
                @if(isset($camera['description']) && $camera['description'])
                <div class="flex justify-between items-start">
                    <span class="text-sm font-medium text-gray-500">Description</span>
                    <span class="text-sm text-gray-900 text-right">{{ $camera['description'] }}</span>
                </div>
                @endif
                <div class="flex justify-between items-start">
                    <span class="text-sm font-medium text-gray-500">Status</span>
                    <span class="text-sm text-right">
                        @if(isset($camera['status']) && $camera['status'] === 'active')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <div class="w-2 h-2 bg-green-400 rounded-full mr-1"></div>
                                Online
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <div class="w-2 h-2 bg-red-400 rounded-full mr-1"></div>
                                Offline
                            </span>
                        @endif
                    </span>
                </div>
                <div class="flex justify-between items-start">
                    <span class="text-sm font-medium text-gray-500">Camera ID</span>
                    <span class="text-sm text-gray-900 text-right font-mono">{{ $camera['id'] ?? 'N/A' }}</span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-6 pt-4 border-t border-gray-200 flex justify-end space-x-3">
                <button onclick="deleteCamera('{{ $camera['id'] }}', '{{ $camera['name'] ?? 'Unknown' }}')"
                        class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Delete Camera
                </button>
            </div>
        </div>

        <!-- Live Stream Section -->
        <div class="bg-gray-50 rounded-lg p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                </svg>
                Live Stream
            </h2>

            <div class="aspect-video bg-gray-900 rounded-lg overflow-hidden mb-4 flex items-center justify-center">
                @if(isset($streamUrl))
                    <iframe 
                        src="{{ $streamUrl }}" 
                        class="w-full h-full border-0"
                        title="Live Camera Stream"
                        allowfullscreen>
                    </iframe>
                @else
                    <div class="text-center text-gray-400">
                        <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        <p class="text-lg font-medium">Stream Not Available</p>
                        <p class="text-sm">The camera stream is currently unavailable</p>
                    </div>
                @endif
            </div>

            <div class="flex space-x-3">
                @if(isset($streamUrl))
                    <a href="{{ $streamUrl }}" 
                       target="_blank"
                       class="flex-1 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                        </svg>
                        Open in New Tab
                    </a>
                @endif
                <button onclick="refreshStream()" 
                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Refresh
                </button>
            </div>

            <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-600 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="text-sm text-blue-800">
                        <p class="font-medium">AI Detection Active</p>
                        <p>This stream includes real-time AI detection overlays and processing.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detection Configuration Section -->
    @if(isset($detectionConfig))
    <div class="mt-6 bg-gray-50 rounded-lg p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            Detection Configuration
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="text-center">
                <div class="text-2xl font-bold text-gray-900">{{ $detectionConfig['record_duration'] ?? 30 }}s</div>
                <div class="text-sm text-gray-500">Record Duration</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold {{ isset($detectionConfig['enable_video']) && $detectionConfig['enable_video'] ? 'text-green-600' : 'text-red-600' }}">
                    {{ isset($detectionConfig['enable_video']) && $detectionConfig['enable_video'] ? 'ON' : 'OFF' }}
                </div>
                <div class="text-sm text-gray-500">Video Recording</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold {{ isset($detectionConfig['enable_screenshot']) && $detectionConfig['enable_screenshot'] ? 'text-green-600' : 'text-red-600' }}">
                    {{ isset($detectionConfig['enable_screenshot']) && $detectionConfig['enable_screenshot'] ? 'ON' : 'OFF' }}
                </div>
                <div class="text-sm text-gray-500">Screenshots</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold {{ isset($detectionConfig['external_endpoint']) && $detectionConfig['external_endpoint'] ? 'text-green-600' : 'text-gray-400' }}">
                    {{ isset($detectionConfig['external_endpoint']) && $detectionConfig['external_endpoint'] ? 'SET' : 'NONE' }}
                </div>
                <div class="text-sm text-gray-500">Webhook</div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Delete Confirmation Modal -->
<div id="delete-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.268 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900">Delete Camera</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Are you sure you want to delete "<span id="camera-name"></span>"? This action cannot be undone.
                </p>
            </div>
            <div class="items-center px-4 py-3 space-x-4">
                <button 
                    id="cancel-delete" 
                    class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md w-auto shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300"
                    onclick="closeDeleteModal()"
                >
                    Cancel
                </button>
                <form id="delete-form" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button 
                        type="submit"
                        class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md w-auto shadow-sm hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-300"
                    >
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
function deleteCamera(cameraId, cameraName) {
    document.getElementById('camera-name').textContent = cameraName;
    document.getElementById('delete-form').action = `{{ route('cctvs.index') }}/${cameraId}`;
    document.getElementById('delete-modal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('delete-modal').classList.add('hidden');
}

function refreshStream() {
    // Refresh the iframe if it exists
    const iframe = document.querySelector('iframe');
    if (iframe) {
        const src = iframe.src;
        iframe.src = '';
        setTimeout(() => {
            iframe.src = src;
        }, 100);
    } else {
        // Reload the page if no iframe
        window.location.reload();
    }
}

// Close modal when clicking outside
document.getElementById('delete-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});
</script>
@endsection