@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Send WhatsApp Broadcast</h1>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white shadow-md rounded-lg p-6">
            <form action="{{ route('broadcast.send.post') }}" method="POST" id="broadcastForm"
                onsubmit="return validateForm()">
                @csrf
                <!-- These hidden inputs are added for tracking -->
                <input type="hidden" name="debug_info" value="Form submitted at {{ now() }}">
                <input type="hidden" name="csrf_token" value="{{ csrf_token() }}">

                <div class="mb-4">
                    <label for="sender_id" class="block text-sm font-medium text-gray-700 mb-1">Sender Account</label>
                    <select name="sender_id" id="sender_id" class="select select-bordered w-full">
                        <option value="">Select a sender</option>
                        @foreach($senders as $sender)
                            <option value="{{ $sender->id }}" {{ old('sender_id') == $sender->id ? 'selected' : '' }}>
                                {{ $sender->name }} ({{ $sender->number_key }})
                            </option>
                        @endforeach
                    </select>
                    @error('sender_id')
                        <p class="text-error text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="data_type" class="block text-sm font-medium text-gray-700 mb-1">Data Type</label>
                    <select name="data_type" id="data_type" class="select select-bordered w-full">
                        <option value="">Select data type</option>
                        @foreach($dataTypes as $value => $label)
                            <option value="{{ $value }}" {{ old('data_type') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('data_type')
                        <p class="text-error text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4" id="results-container" style="display: none;">
                    <label for="result_id" class="block text-sm font-medium text-gray-700 mb-1">Select Detection
                        Result</label>
                    <select name="result_id" id="result_id" class="select select-bordered w-full">
                        <option value="">Select a detection result</option>
                    </select>
                    <div id="loading-results" class="mt-2 text-gray-600 text-sm flex items-center" style="display: none;">
                        <span class="loading loading-spinner loading-sm mr-2"></span>
                        Loading results...
                    </div>
                    <div id="no-results" class="alert alert-error mt-2 py-2 text-sm" style="display: none;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-4 w-4" fill="none"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>No detection results available for this data type.</span>
                    </div>
                    @error('result_id')
                        <p class="text-error text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4 border-t pt-4 mt-4">
                    <div class="flex justify-between items-center">
                        <h3 class="text-sm font-medium text-gray-700">Broadcast Preview</h3>
                        <button type="button" id="togglePreview" class="text-xs text-blue-500">Show Preview</button>
                    </div>
                    <div id="messagePreview"
                        class="mt-2 p-3 bg-gray-50 rounded text-sm font-mono whitespace-pre-line hidden">
                        Select a result to see preview
                    </div>
                </div>


                <div class="flex items-center justify-between mt-6">
                    <div class="flex space-x-2">
                        <a href="{{ route('dashboard') }}" class="btn btn-ghost">
                            Back to Dashboard</a>
                    </div>
                    <button type="submit" id="submitButton" class="btn btn-primary">
                        Send Broadcast
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            // Form validation function
            function validateForm() {
                const senderValue = document.getElementById('sender_id').value;
                const dataTypeValue = document.getElementById('data_type').value;
                const resultValue = document.getElementById('result_id').value;

                console.log('Validating form submission:');
                console.log('- Sender:', senderValue);
                console.log('- Data Type:', dataTypeValue);
                console.log('- Result:', resultValue);

                if (!senderValue) {
                    alert('Please select a sender account');
                    return false;
                }

                if (!dataTypeValue) {
                    alert('Please select a data type');
                    return false;
                }

                if (!resultValue) {
                    alert('Please select a detection result');
                    return false;
                }

                // Show loading state
                const submitButton = document.getElementById('submitButton');
                submitButton.innerHTML = `
                                                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                                </svg>
                                                                Sending...
                                                            `;
                submitButton.disabled = true;

                // Add a class to show that we're submitting
                document.querySelector('.bg-white.shadow-md.rounded-lg').classList.add('opacity-75');

                return true;
            }

            document.addEventListener('DOMContentLoaded', function () {
                console.log('DOM loaded - initializing broadcast form');

                // Debug function to help troubleshoot
                function debugLog(message, data = null) {
                    const timestamp = new Date().toLocaleTimeString();
                    if (data) {
                        console.log(`[${timestamp}] ${message}`, data);
                    } else {
                        console.log(`[${timestamp}] ${message}`);
                    }
                }

                // DOM elements
                const dataTypeSelect = document.getElementById('data_type');
                const resultsContainer = document.getElementById('results-container');
                const resultSelect = document.getElementById('result_id');
                const submitButton = document.getElementById('submitButton');
                const senderSelect = document.getElementById('sender_id');
                const form = document.getElementById('broadcastForm');

                // Disable submit button initially
                submitButton.disabled = true;

                // Form validation function
                function checkFormValidity() {
                    debugLog('Checking form validity:');
                    debugLog(`- Sender: ${senderSelect.value || 'not selected'}`);
                    debugLog(`- Data Type: ${dataTypeSelect.value || 'not selected'}`);
                    debugLog(`- Result: ${resultSelect.value || 'not selected'}`);

                    // Only enable the button if all required fields are filled
                    if (senderSelect.value && dataTypeSelect.value && resultSelect.value) {
                        submitButton.disabled = false;
                        debugLog('Form is valid - enabling submit button');
                    } else {
                        submitButton.disabled = true;
                        debugLog('Form is incomplete - submit button disabled');
                    }
                }

                // Add event listeners to form controls
                senderSelect.addEventListener('change', checkFormValidity);

                // Handle toggle preview button
                const togglePreviewBtn = document.getElementById('togglePreview');
                const messagePreview = document.getElementById('messagePreview');

                togglePreviewBtn.addEventListener('click', function () {
                    if (messagePreview.classList.contains('hidden')) {
                        messagePreview.classList.remove('hidden');
                        togglePreviewBtn.textContent = 'Hide Preview';

                        // Update preview content
                        updatePreviewContent();
                    } else {
                        messagePreview.classList.add('hidden');
                        togglePreviewBtn.textContent = 'Show Preview';
                    }
                });

                // Function to update the preview content
                function updatePreviewContent() {
                    if (!resultSelect.value) {
                        messagePreview.textContent = 'Select a result to see preview';
                    } else {
                        const dataType = dataTypeSelect.value;
                        const selectedOption = resultSelect.options[resultSelect.selectedIndex];
                        const previewText = `AIControl Alert\n\n${dataType === 'cctv' ? '*CCTV Detection Alert*' : '*Social Media Alert*'}\nTime: ${new Date().toLocaleString()}\n${selectedOption.textContent}\n\nRecipients will be notified based on their preferences.`;
                        messagePreview.textContent = previewText;
                    }
                }

                resultSelect.addEventListener('change', function () {
                    checkFormValidity();

                    // Update preview if it's visible
                    if (!messagePreview.classList.contains('hidden')) {
                        updatePreviewContent();
                    }
                });
                // Handle data type change
                dataTypeSelect.addEventListener('change', function () {
                    const dataType = this.value;
                    console.log('Data type changed to:', dataType);

                    // Clear and hide results if no data type selected
                    if (!dataType) {
                        resultsContainer.style.display = 'none';
                        resultSelect.innerHTML = '<option value="">Select a detection result</option>';
                        checkFormValidity();
                        return;
                    }

                    // Show results container with loading message
                    resultsContainer.style.display = 'block';
                    document.getElementById('loading-results').style.display = 'flex';
                    document.getElementById('no-results').style.display = 'none';
                    resultSelect.innerHTML = '<option value="">Select a detection result</option>';

                    // Fetch detection results based on the selected data type
                    fetch(`{{ route('broadcast.get-detection-results') }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('input[name="csrf_token"]').value
                        },
                        body: JSON.stringify({ data_type: dataType })
                    })
                        .then(response => response.json())
                        .then(data => {
                            // Hide loading indicator
                            document.getElementById('loading-results').style.display = 'none';

                            if (data.results && data.results.length > 0) {
                                // Populate dropdown with results
                                data.results.forEach(result => {
                                    const option = document.createElement('option');
                                    option.value = result.id;
                                    option.textContent = result.summary || `Detection #${result.id} - ${result.created_at}`;
                                    resultSelect.appendChild(option);
                                });
                                debugLog(`Loaded ${data.results.length} results for ${dataType}`);
                            } else {
                                // Show no results message
                                document.getElementById('no-results').style.display = 'block';
                                debugLog(`No results found for ${dataType}`);
                            }

                            checkFormValidity();
                        })
                        .catch(error => {
                            console.error('Error fetching detection results:', error);
                            document.getElementById('loading-results').style.display = 'none';
                            document.getElementById('no-results').style.display = 'block';
                            document.getElementById('no-results').textContent = 'Error loading results. Please try again.';
                        });
                });
            });
        </script>
    @endpush

@endsection