@extends('layouts.app')

@section('title', 'CCTV Camera Feeds')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex items-center mb-6">
            <a href="{{ route('home') }}" class="text-gray-600 hover:text-gray-900 mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">CCTV Camera Feeds</h1>
        </div>

        @if (isset($error))
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    {{ $error }}
                </div>
            </div>
        @endif

        @if(isset($cameras) && count($cameras) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($cameras as $camera)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $camera['name'] ?? 'Unknown Camera' }}</h3>
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
                            </div>
                            
                            <p class="text-sm text-gray-600 mb-4">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                {{ $camera['location'] ?? 'Unknown location' }}
                            </p>

                            <div class="aspect-video bg-gray-900 rounded-lg mb-4 flex items-center justify-center overflow-hidden">
                                @if(isset($camera['status']) && $camera['status'] === 'active')
                                    <iframe 
                                        src="{{ config('cctv.service.base_url') }}/stream/{{ $camera['id'] }}" 
                                        class="w-full h-full border-0"
                                        title="Live Camera Stream - {{ $camera['name'] }}"
                                        loading="lazy">
                                    </iframe>
                                @else
                                    <div class="text-center text-gray-400">
                                        <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                        </svg>
                                        <p class="text-sm">Camera Offline</p>
                                    </div>
                                @endif
                            </div>

                            <div class="flex justify-between items-center">
                                <div class="text-xs text-gray-500">
                                    {{ $camera['ip_address'] ?? 'N/A' }}
                                </div>
                                @if(isset($camera['status']) && $camera['status'] === 'active')
                                    <a href="{{ config('cctv.service.base_url') }}/stream/{{ $camera['id'] }}" 
                                       target="_blank" 
                                       class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm transition duration-200 flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                        </svg>
                                        Full Screen
                                    </a>
                                @else
                                    <span class="bg-gray-300 text-gray-500 px-3 py-1 rounded text-sm cursor-not-allowed">
                                        Unavailable
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Auto-refresh notice -->
            <div class="mt-8 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="text-sm text-blue-800">
                        <p class="font-medium">Live Feeds with AI Detection</p>
                        <p>All streams include real-time AI processing for motion and object detection.</p>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No cameras available</h3>
                <p class="mt-1 text-sm text-gray-500">
                    There are currently no CCTV cameras available to view, or the service is temporarily unavailable.
                </p>
                <div class="mt-6">
                    <button 
                        onclick="window.location.reload()" 
                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg inline-flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Refresh
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>

@endsection

@section('scripts')
<script>
// Auto-refresh page every 5 minutes to keep streams updated
setTimeout(function() {
    window.location.reload();
}, 300000); // 5 minutes

// Handle iframe loading errors
document.addEventListener('DOMContentLoaded', function() {
    const iframes = document.querySelectorAll('iframe');
    iframes.forEach(function(iframe) {
        iframe.addEventListener('error', function() {
            const parent = this.parentElement;
            parent.innerHTML = `
                <div class="text-center text-gray-400">
                    <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.268 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    <p class="text-sm">Stream Error</p>
                </div>
            `;
        });
    });
});
</script>
@endsection