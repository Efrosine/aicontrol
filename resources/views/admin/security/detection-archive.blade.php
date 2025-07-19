@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Detection Archive</h1>
                <p class="text-gray-600">
                    Browse and manage recorded detection files from security cameras
                    @if(isset($showAllDates) && $showAllDates)
                        <span class="badge badge-info badge-sm ml-2">Showing All Dates</span>
                    @endif
                </p>
                <p class="text-sm text-gray-500 mt-1">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Files from unregistered cameras are marked as "Unidentified" with a warning icon.
                </p>
                <!-- Storage Status Indicator -->
                <div class="flex items-center mt-2">
                    <span class="text-sm text-gray-500 mr-2">Storage Status:</span>
                    <div class="flex items-center">
                        <div class="w-2 h-2 rounded-full mr-2 {{ $storageStatus['status'] === 'online' ? 'bg-green-500' : 'bg-red-500' }}"></div>
                        <span class="text-sm font-medium {{ $storageStatus['status'] === 'online' ? 'text-green-600' : 'text-red-600' }}">
                            {{ $storageStatus['status'] === 'online' ? 'Connected' : 'Not Connected' }}
                        </span>
                        @if($storageStatus['status'] === 'online' && isset($storageStatus['response_time']))
                            <span class="text-xs text-gray-500 ml-1">({{ number_format($storageStatus['response_time'] * 1000, 0) }}ms)</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <div class="badge badge-info">
                    {{ count($detectionFiles) }} files found
                    @if(isset($showAllDates) && $showAllDates)
                        <span class="ml-1 text-xs opacity-75">(all dates)</span>
                    @endif
                </div>
                <a href="{{ route('admin.storage.settings.index') }}" class="btn btn-outline btn-sm">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Storage Settings
                </a>
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
                        <option value="{{ $camera->id }}" {{ $selectedCamera == $camera->id ? 'selected' : '' }}
                                @if(!$camera->is_identified) style="color: #d97706; font-style: italic;" @endif>
                            @if(!$camera->is_identified)⚠ @endif{{ $camera->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Date Filter -->
            <div class="form-control">
                <label class="label">
                    <span class="label-text font-medium">Date</span>
                </label>
                <div class="flex flex-col gap-2">
                    <select name="date_filter_type" class="select select-bordered w-full" onchange="handleDateFilterChange(this)">
                        <option value="specific" {{ (!isset($showAllDates) || !$showAllDates) ? 'selected' : '' }}>Specific Date</option>
                        <option value="all" {{ (isset($showAllDates) && $showAllDates) ? 'selected' : '' }}>Show All Dates</option>
                    </select>
                    <div id="specificDateInput" class="{{ (isset($showAllDates) && $showAllDates) ? 'hidden' : '' }}">
                        <input type="date" name="date" value="{{ $selectedDate }}" class="input input-bordered w-full" onchange="this.form.submit()">
                    </div>
                    <input type="hidden" name="show_all_dates" value="{{ (isset($showAllDates) && $showAllDates) ? '1' : '0' }}">
                </div>
            </div>

            <!-- Detection Type Filter -->
            <div class="form-control">
                <label class="label">
                    <span class="label-text font-medium">Detection Type</span>
                </label>
                <select name="detection_type" class="select select-bordered w-full" onchange="this.form.submit()">
                    <option value="all" {{ $selectedDetectionType == 'all' || !$selectedDetectionType ? 'selected' : '' }}>All Types</option>
                    @foreach($detectionTypes as $type)
                        <option value="{{ $type }}" {{ $selectedDetectionType == $type ? 'selected' : '' }}>
                            {{ ucfirst($type) }}
                        </option>
                    @endforeach
                    @if(empty($detectionTypes))
                        <option value="person" {{ $selectedDetectionType == 'person' ? 'selected' : '' }} disabled>No detection types found</option>
                    @endif
                </select>
            </div>

            <!-- Time Range -->
            <div class="form-control">
                <label class="label">
                    <span class="label-text font-medium">Time Range</span>
                </label>
                <select name="time_range" class="select select-bordered w-full" onchange="this.form.submit()">
                    <option value="all" {{ $selectedTimeRange == 'all' || !$selectedTimeRange ? 'selected' : '' }}>All Day</option>
                    <option value="morning" {{ $selectedTimeRange == 'morning' ? 'selected' : '' }}>Morning (06:00-12:00)</option>
                    <option value="afternoon" {{ $selectedTimeRange == 'afternoon' ? 'selected' : '' }}>Afternoon (12:00-18:00)</option>
                    <option value="evening" {{ $selectedTimeRange == 'evening' ? 'selected' : '' }}>Evening (18:00-24:00)</option>
                    <option value="night" {{ $selectedTimeRange == 'night' ? 'selected' : '' }}>Night (00:00-06:00)</option>
                </select>
            </div>
        </form>
    </div>

    <!-- Detection Files Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Detection Files</h3>
                <div class="flex items-center space-x-4 text-sm">
                    <div class="flex items-center">
                        <div class="badge badge-outline badge-sm">Identified</div>
                        <span class="text-gray-500 ml-1">- Registered camera</span>
                    </div>
                    <div class="flex items-center">
                        <div class="badge badge-warning badge-sm flex items-center gap-1">
                            <svg class="w-2 h-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            Unidentified
                        </div>
                        <span class="text-gray-500 ml-1">- Unknown camera</span>
                    </div>
                </div>
            </div>
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
                                @if(isset($file['camera_is_identified']) && !$file['camera_is_identified'])
                                    <div class="flex items-center">
                                        <div class="badge badge-warning flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                            </svg>
                                            {{ $file['camera_name'] }}
                                        </div>
                                        <span class="text-xs text-yellow-600 ml-2" title="Camera ID: {{ $file['camera_id'] }}">
                                            (ID: {{ $file['camera_id'] }})
                                        </span>
                                    </div>
                                @else
                                    <div class="badge badge-outline">{{ $file['camera_name'] }}</div>
                                @endif
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
                                <div class="flex justify-center space-x-2">
                                    <button class="btn btn-sm btn-outline btn-primary" onclick="previewFile('{{ $file['id'] }}', '{{ $file['type'] }}', '{{ $file['filename'] }}', '{{ $file['full_path'] }}')">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        View
                                    </button>
                                    <button class="btn btn-sm btn-outline btn-success" onclick="downloadFile('{{ $file['id'] }}', '{{ $file['filename'] }}', '{{ $file['full_path'] }}')">
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
                            <td colspan="7" class="text-center py-12">
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
let currentFilePath = null;

function previewFile(fileId, type, filename, filePath) {
    currentFileId = fileId;
    currentFileName = filename;
    currentFilePath = filePath;
    
    document.getElementById('previewTitle').textContent = `Preview: ${filename}`;
    document.getElementById('previewModal').showModal();
    
    // Show loading
    const previewContent = document.getElementById('previewContent');
    previewContent.innerHTML = '<div class="loading loading-spinner loading-lg"></div>';
    
    // Make API call for preview
    fetch(`{{ route('admin.security.detection-archive.preview') }}?file_id=${encodeURIComponent(fileId)}&file_path=${encodeURIComponent(filePath)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.type === 'video') {
                    previewContent.innerHTML = `
                        <div class="w-full">
                            <video controls class="w-full max-h-96 rounded-lg">
                                <source src="${data.url}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        </div>`;
                } else if (data.type === 'image') {
                    previewContent.innerHTML = `
                        <div class="w-full">
                            <img src="${data.url}" alt="${filename}" class="w-full max-h-96 object-contain rounded-lg">
                        </div>`;
                } else {
                    previewContent.innerHTML = `
                        <div class="w-full">
                            <div class="bg-gray-200 rounded-lg flex items-center justify-center h-96">
                                <div class="text-center text-gray-600">
                                    <svg class="w-16 h-16 mx-auto mb-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/>
                                    </svg>
                                    <p class="text-lg font-medium">File Preview Not Available</p>
                                    <p class="text-sm opacity-75">${filename}</p>
                                    <button class="btn btn-primary btn-sm mt-2" onclick="downloadCurrentFile()">Download File</button>
                                </div>
                            </div>
                        </div>`;
                }
            } else {
                previewContent.innerHTML = `
                    <div class="w-full">
                        <div class="bg-red-50 rounded-lg flex items-center justify-center h-96">
                            <div class="text-center text-red-600">
                                <svg class="w-16 h-16 mx-auto mb-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12,2L13.09,8.26L22,9L13.09,9.74L12,16L10.91,9.74L2,9L10.91,8.26L12,2Z"/>
                                </svg>
                                <p class="text-lg font-medium">Preview Failed</p>
                                <p class="text-sm opacity-75">${data.message}</p>
                            </div>
                        </div>
                    </div>`;
            }
        })
        .catch(error => {
            console.error('Preview error:', error);
            previewContent.innerHTML = `
                <div class="w-full">
                    <div class="bg-red-50 rounded-lg flex items-center justify-center h-96">
                        <div class="text-center text-red-600">
                            <svg class="w-16 h-16 mx-auto mb-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12,2L13.09,8.26L22,9L13.09,9.74L12,16L10.91,9.74L2,9L10.91,8.26L12,2Z"/>
                            </svg>
                            <p class="text-lg font-medium">Preview Error</p>
                            <p class="text-sm opacity-75">Unable to load preview</p>
                        </div>
                    </div>
                </div>`;
        });
}

function downloadFile(fileId, filename, filePath) {
    console.log(`Downloading file: ${filename} (ID: ${fileId})`);
    
    // Show download notification
    showToast('info', `Preparing download for ${filename}...`);
    
    // Make API call for download
    fetch(`{{ route('admin.security.detection-archive.download') }}?file_id=${encodeURIComponent(fileId)}&file_path=${encodeURIComponent(filePath)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Create a temporary anchor element to trigger download
                const link = document.createElement('a');
                link.href = data.download_url;
                link.download = data.filename;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                
                showToast('success', `Download started for ${data.filename} (${data.size})`);
            } else {
                showToast('error', `Download failed: ${data.message}`);
            }
        })
        .catch(error => {
            console.error('Download error:', error);
            showToast('error', `Download failed for ${filename}`);
        });
}

function downloadCurrentFile() {
    if (currentFileId && currentFileName && currentFilePath) {
        downloadFile(currentFileId, currentFileName, currentFilePath);
    }
}

function refreshArchive() {
    location.reload();
}

function handleDateFilterChange(selectElement) {
    const form = selectElement.closest('form');
    const specificDateInput = document.getElementById('specificDateInput');
    const showAllDatesInput = document.querySelector('input[name="show_all_dates"]');
    
    if (selectElement.value === 'all') {
        // Show All Dates selected
        specificDateInput.classList.add('hidden');
        showAllDatesInput.value = '1';
    } else {
        // Specific Date selected
        specificDateInput.classList.remove('hidden');
        showAllDatesInput.value = '0';
    }
    
    form.submit();
}



function showToast(type, message) {
    const toast = document.createElement('div');
    toast.className = 'toast toast-top toast-end';
    
    const alertClass = type === 'success' ? 'alert-success' : 
                      type === 'error' ? 'alert-error' : 'alert-info';
    
    const icon = type === 'success' ? 
        `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>` :
        type === 'error' ?
        `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>` :
        `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>`;
    
    toast.innerHTML = `
        <div class="alert ${alertClass}">
            ${icon}
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    // Remove toast after 3 seconds
    setTimeout(() => {
        if (document.body.contains(toast)) {
            document.body.removeChild(toast);
        }
    }, 3000);
}

// Auto-refresh every 30 seconds (optional)
// setInterval(refreshArchive, 30000);
</script>
@endsection
