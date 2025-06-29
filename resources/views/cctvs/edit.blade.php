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

            <!-- IP Address -->
            <div>
                <label for="ip_address" class="block text-sm font-medium text-gray-700 mb-2">
                    IP Address <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="ip_address" 
                    id="ip_address" 
                    value="{{ old('ip_address', $camera['ip_address'] ?? '') }}" 
                    required
                    pattern="^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('ip_address') border-red-500 @enderror"
                    placeholder="e.g., 192.168.1.100"
                >
                @error('ip_address')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Port -->
            <div>
                <label for="port" class="block text-sm font-medium text-gray-700 mb-2">
                    Port <span class="text-red-500">*</span>
                </label>
                <input 
                    type="number" 
                    name="port" 
                    id="port" 
                    value="{{ old('port', $camera['port'] ?? 80) }}" 
                    min="1" 
                    max="65535"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('port') border-red-500 @enderror"
                    placeholder="e.g., 80, 554, 8080"
                >
                @error('port')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Username -->
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                    Username
                </label>
                <input 
                    type="text" 
                    name="username" 
                    id="username" 
                    value="{{ old('username', $camera['username'] ?? '') }}" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('username') border-red-500 @enderror"
                    placeholder="Camera username (optional)"
                >
                @error('username')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                    Password
                </label>
                <input 
                    type="password" 
                    name="password" 
                    id="password" 
                    value="{{ old('password', $camera['password'] ?? '') }}" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror"
                    placeholder="Leave blank to keep current password"
                >
                @error('password')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">Leave blank to keep the current password</p>
            </div>
        </div>

        <!-- Description -->
        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                Description
            </label>
            <textarea 
                name="description" 
                id="description" 
                rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror"
                placeholder="Additional details about this camera..."
            >{{ old('description', $camera['description'] ?? '') }}</textarea>
            @error('description')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Enabled Status -->
        <div class="flex items-center">
            <input 
                type="checkbox" 
                name="enabled" 
                id="enabled" 
                value="1"
                {{ old('enabled', $camera['enabled'] ?? false) ? 'checked' : '' }}
                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
            >
            <label for="enabled" class="ml-2 block text-sm text-gray-900">
                Camera is enabled and active
            </label>
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
// IP address validation
document.getElementById('ip_address').addEventListener('input', function(e) {
    const value = e.target.value;
    const ipPattern = /^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/;
    
    if (value && !ipPattern.test(value)) {
        e.target.setCustomValidity('Please enter a valid IP address');
    } else {
        e.target.setCustomValidity('');
    }
});

// Port validation
document.getElementById('port').addEventListener('input', function(e) {
    const value = parseInt(e.target.value);
    
    if (value < 1 || value > 65535) {
        e.target.setCustomValidity('Port must be between 1 and 65535');
    } else {
        e.target.setCustomValidity('');
    }
});
</script>
@endsection