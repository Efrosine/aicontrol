@extends('layouts.app')

@section('title', 'Edit CCTV Camera')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex items-center mb-6">
        <a href="{{ route('cctvs.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Edit CCTV Camera</h1>
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

    <form action="{{ route('cctvs.update', $camera['id']) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Camera Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Camera Name <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="name" 
                    id="name" 
                    value="{{ old('name', $camera['name'] ?? '') }}" 
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                    placeholder="e.g., Front Entrance Camera"
                >
                @error('name')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Location -->
            <div>
                <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
                    Location <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="location" 
                    id="location" 
                    value="{{ old('location', $camera['location'] ?? '') }}" 
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('location') border-red-500 @enderror"
                    placeholder="e.g., Building A - Main Entrance"
                >
                @error('location')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- IP Address or URL -->
        <div>
            <label for="ip_address" class="block text-sm font-medium text-gray-700 mb-2">
                IP Address or URL <span class="text-red-500">*</span>
            </label>
            <input 
                type="text" 
                name="ip_address" 
                id="ip_address" 
                value="{{ old('ip_address', $camera['ip_address'] ?? '') }}" 
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('ip_address') border-red-500 @enderror"
                placeholder="e.g., 192.168.1.100 or http://camera.example.com:8080"
            >
            <p class="mt-1 text-sm text-gray-500">Enter the camera's IP address or full URL including protocol and port</p>
            @error('ip_address')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit Buttons -->
        <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
            <a href="{{ route('cctvs.index') }}" 
               class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition duration-200">
                Cancel
            </a>
            <button 
                type="submit" 
                class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition duration-200 flex items-center"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Update Camera
            </button>
        </div>
    </form>
</div>

@endsection

@section('scripts')
<script>
// IP address or URL validation
document.getElementById('ip_address').addEventListener('input', function(e) {
    const value = e.target.value.trim();
    
    if (!value) {
        e.target.setCustomValidity('IP address or URL is required');
        return;
    }
    
    // Check if it's a URL (starts with http:// or https://)
    const urlPattern = /^https?:\/\/.+/i;
    // Check if it's an IP address
    const ipPattern = /^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/;
    
    if (urlPattern.test(value) || ipPattern.test(value)) {
        e.target.setCustomValidity('');
    } else {
        e.target.setCustomValidity('Please enter a valid IP address (e.g., 192.168.1.100) or URL (e.g., http://camera.example.com:8080)');
    }
});
</script>
@endsection