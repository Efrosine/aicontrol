@extends('layouts.app')

@section('title', 'CCTV Settings')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">CCTV Service Settings</h1>
        <div class="flex space-x-2">
            <button 
                id="test-connection-btn" 
                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center"
                onclick="testConnection()"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Test Connection
            </button>
        </div>
    </div>

    <!-- Service Status -->
    <div class="mb-6 p-4 rounded-lg {{ $serviceStatus['status'] === 'online' ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200' }}">
        <div class="flex items-center">
            <div class="w-3 h-3 rounded-full mr-3 {{ $serviceStatus['status'] === 'online' ? 'bg-green-500' : 'bg-red-500' }}"></div>
            <div>
                <h3 class="font-medium {{ $serviceStatus['status'] === 'online' ? 'text-green-800' : 'text-red-800' }}">
                    Service Status: {{ ucfirst($serviceStatus['status']) }}
                </h3>
                @if($serviceStatus['status'] === 'online')
                    <p class="text-sm text-green-600">
                        Connected to {{ $settings['base_url'] }}
                        @if(isset($serviceStatus['response_time']))
                            ({{ number_format($serviceStatus['response_time'] * 1000, 0) }}ms)
                        @endif
                    </p>
                @else
                    <p class="text-sm text-red-600">
                        Cannot connect to {{ $settings['base_url'] }}
                        @if(isset($serviceStatus['error']))
                            - {{ $serviceStatus['error'] }}
                        @endif
                    </p>
                @endif
            </div>
        </div>
    </div>

    <!-- Settings Form -->
    <form action="{{ route('settings.cctv.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Base URL -->
            <div class="md:col-span-2">
                <label for="base_url" class="block text-sm font-medium text-gray-700 mb-2">
                    CCTV Service Base URL <span class="text-red-500">*</span>
                </label>
                <input 
                    type="url" 
                    id="base_url" 
                    name="base_url" 
                    value="{{ old('base_url', $settings['base_url']) }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('base_url') border-red-500 @enderror"
                    placeholder="http://192.168.8.109:8000"
                    required
                >
                @error('base_url')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">
                    The base URL of your external CCTV service (including protocol and port)
                </p>
            </div>

            <!-- Timeout -->
            <div>
                <label for="timeout" class="block text-sm font-medium text-gray-700 mb-2">
                    Request Timeout (seconds) <span class="text-red-500">*</span>
                </label>
                <input 
                    type="number" 
                    id="timeout" 
                    name="timeout" 
                    value="{{ old('timeout', $settings['timeout']) }}"
                    min="5" 
                    max="300"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('timeout') border-red-500 @enderror"
                    required
                >
                @error('timeout')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">
                    Maximum time to wait for API responses (5-300 seconds)
                </p>
            </div>

            <!-- Connect Timeout -->
            <div>
                <label for="connect_timeout" class="block text-sm font-medium text-gray-700 mb-2">
                    Connection Timeout (seconds) <span class="text-red-500">*</span>
                </label>
                <input 
                    type="number" 
                    id="connect_timeout" 
                    name="connect_timeout" 
                    value="{{ old('connect_timeout', $settings['connect_timeout']) }}"
                    min="5" 
                    max="60"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('connect_timeout') border-red-500 @enderror"
                    required
                >
                @error('connect_timeout')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">
                    Maximum time to wait for initial connection (5-60 seconds)
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
                    value="{{ old('retry_attempts', $settings['retry_attempts']) }}"
                    min="1" 
                    max="10"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('retry_attempts') border-red-500 @enderror"
                    required
                >
                @error('retry_attempts')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">
                    Number of times to retry failed requests (1-10)
                </p>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end space-x-4 mt-8">
            <a 
                href="{{ route('cctvs.index') }}" 
                class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition duration-200"
            >
                Cancel
            </a>
            <button 
                type="submit" 
                class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition duration-200"
            >
                Save Settings
            </button>
        </div>
    </form>
</div>

<!-- Connection Test Modal -->
<div id="connection-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div id="modal-icon" class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 mb-4">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
            </div>
            <h3 id="modal-title" class="text-lg leading-6 font-medium text-gray-900">Testing Connection...</h3>
            <div id="modal-content" class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Please wait while we test the connection to your CCTV service.
                </p>
            </div>
            <div id="modal-actions" class="items-center px-4 py-3">
                <button 
                    id="modal-close" 
                    class="px-4 py-2 bg-blue-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-300"
                    onclick="closeModal()"
                >
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
function testConnection() {
    const btn = document.getElementById('test-connection-btn');
    const modal = document.getElementById('connection-modal');
    const modalIcon = document.getElementById('modal-icon');
    const modalTitle = document.getElementById('modal-title');
    const modalContent = document.getElementById('modal-content');
    const modalActions = document.getElementById('modal-actions');
    
    // Show modal
    modal.classList.remove('hidden');
    
    // Reset modal to loading state
    modalIcon.innerHTML = `
        <svg class="w-6 h-6 text-blue-600 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
        </svg>
    `;
    modalIcon.className = 'mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 mb-4';
    modalTitle.textContent = 'Testing Connection...';
    modalContent.innerHTML = '<p class="text-sm text-gray-500">Please wait while we test the connection to your CCTV service.</p>';
    modalActions.innerHTML = `
        <button 
            id="modal-close" 
            class="px-4 py-2 bg-gray-300 text-gray-700 text-base font-medium rounded-md w-full shadow-sm cursor-not-allowed"
            disabled
        >
            Testing...
        </button>
    `;
    
    // Disable test button
    btn.disabled = true;
    btn.innerHTML = `
        <svg class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
        </svg>
        Testing...
    `;
    
    // Make request
    fetch('{{ route("settings.cctv.test") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        // Update modal based on response
        if (data.status === 'online') {
            modalIcon.innerHTML = `
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            `;
            modalIcon.className = 'mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4';
            modalTitle.textContent = 'Connection Successful!';
            modalContent.innerHTML = `
                <p class="text-sm text-gray-500">
                    Successfully connected to the CCTV service.
                    ${data.response_time ? `Response time: ${Math.round(data.response_time * 1000)}ms` : ''}
                </p>
            `;
        } else {
            modalIcon.innerHTML = `
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            `;
            modalIcon.className = 'mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4';
            modalTitle.textContent = 'Connection Failed';
            modalContent.innerHTML = `
                <p class="text-sm text-gray-500">
                    Failed to connect to the CCTV service.
                    ${data.error ? `Error: ${data.error}` : ''}
                </p>
            `;
        }
        
        modalActions.innerHTML = `
            <button 
                id="modal-close" 
                class="px-4 py-2 bg-blue-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-300"
                onclick="closeModal()"
            >
                Close
            </button>
        `;
    })
    .catch(error => {
        modalIcon.innerHTML = `
            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        `;
        modalIcon.className = 'mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4';
        modalTitle.textContent = 'Connection Failed';
        modalContent.innerHTML = `
            <p class="text-sm text-gray-500">
                An error occurred while testing the connection: ${error.message}
            </p>
        `;
        modalActions.innerHTML = `
            <button 
                id="modal-close" 
                class="px-4 py-2 bg-blue-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-300"
                onclick="closeModal()"
            >
                Close
            </button>
        `;
    })
    .finally(() => {
        // Re-enable test button
        btn.disabled = false;
        btn.innerHTML = `
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Test Connection
        `;
    });
}

function closeModal() {
    document.getElementById('connection-modal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('connection-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>
@endsection
