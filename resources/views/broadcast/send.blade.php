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
                    <select name="sender_id" id="sender_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select a sender</option>
                        @foreach($senders as $sender)
                            <option value="{{ $sender->id }}" {{ old('sender_id') == $sender->id ? 'selected' : '' }}>
                                {{ $sender->name }} ({{ $sender->number_key }})
                            </option>
                        @endforeach
                    </select>
                    @error('sender_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="data_type" class="block text-sm font-medium text-gray-700 mb-1">Data Type</label>
                    <select name="data_type" id="data_type"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select data type</option>
                        @foreach($dataTypes as $value => $label)
                            <option value="{{ $value }}" {{ old('data_type') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('data_type')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4" id="results-container" style="display: none;">
                    <label for="result_id" class="block text-sm font-medium text-gray-700 mb-1">Select Detection
                        Result</label>
                    <select name="result_id" id="result_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select a detection result</option>
                    </select>
                    <div id="loading-results" class="mt-2 text-gray-600 text-sm flex items-center" style="display: none;">
                        <svg class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        Loading results...
                    </div>
                    <div id="no-results" class="mt-2 text-red-600 text-sm" style="display: none;">
                        No detection results available for this data type.
                    </div>
                    @error('result_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
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

                <div class="mb-4 border-t pt-4 mt-4">
                    <div class="flex justify-between items-center">
                        <h3 class="text-sm font-medium text-gray-700">Debug Info</h3>
                        <button type="button" id="toggleDebug" class="text-xs text-blue-500">Show Debug Info</button>
                    </div>
                    <div id="debugInfo" class="mt-2 p-3 bg-gray-50 rounded text-sm font-mono hidden">
                        No debug info available
                    </div>
                </div>

                <div class="flex items-center justify-between mt-6">
                    <div class="flex space-x-2">
                        <a href="{{ route('dashboard') }}" class="text-blue-500 hover:text-blue-700">
                            Back to Dashboard
                        </a>
                        <button type="button" id="testDataButton"
                            class="text-gray-500 hover:text-gray-700 text-sm underline">
                            Test Data
                        </button>
                    </div>
                    <button type="submit" id="submitButton"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" disabled>
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

                    // Update debug info if visible
                    const debugInfo = document.getElementById('debugInfo');
                    if (debugInfo && !debugInfo.classList.contains('hidden')) {
                        const newMessage = document.createElement('div');
                        newMessage.innerHTML = `<p class="text-xs">[${timestamp}] ${message}</p>`;
                        debugInfo.prepend(newMessage);
                    }
                }

                // DOM elements
                const dataTypeSelect = document.getElementById('data_type');
                const resultsContainer = document.getElementById('results-container');
                const resultSelect = document.getElementById('result_id');
                const submitButton = document.getElementById('submitButton');
                const senderSelect = document.getElementById('sender_id');
                const testDataButton = document.getElementById('testDataButton');
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
                resultSelect.addEventListener('change', function () {
                    checkFormValidity();

                    // Update preview if it's visible
                    const preview = document.getElementById('messagePreview');
                    if (!preview.classList.contains('hidden')) {
                        if (!resultSelect.value) {
                            preview.textContent = 'Select a result to see preview';
                        } else {
                            const dataType = dataTypeSelect.value;
                            const selectedOption = resultSelect.options[resultSelect.selectedIndex];
                            const previewText = `AIControl Alert\n\n${dataType === 'cctv' ? '*CCTV Detection Alert*' : '*Social Media Alert*'}\nTime: ${new Date().toLocaleString()}\n${selectedOption.textContent}\n\nRecipients will be notified based on their preferences.`;
                            preview.textContent = previewText;
                        }
                    }
                });

                // Test data button
                testDataButton.addEventListener('click', function () {
                    debugLog('Test data button clicked');

                    // Set a default sender if available
                    if (senderSelect.options.length > 1) {
                        senderSelect.selectedIndex = 1; // First non-empty option
                        debugLog(`Selected sender: ${senderSelect.options[senderSelect.selectedIndex].text}`);
                    } else {
                        debugLog('No sender options available');
                    }

                    // Set data type to CCTV
                    dataTypeSelect.value = 'cctv';
                    debugLog('Set data type to CCTV');

                    // Trigger the change event to load results
                    debugLog('Triggering data type change event');
                    const event = new Event('change');
                    dataTypeSelect.dispatchEvent(event);

                    // Notify the user
                    setTimeout(() => {
                        debugLog('Test data loaded');
                        alert('Test data loaded. Select a detection result to enable the Send button.');
                    }, 500);
                });

                // Debug info toggle
                document.getElementById('toggleDebug').addEventListener('click', function () {
                    const debugInfo = document.getElementById('debugInfo');
                    const button = this;

                    if (debugInfo.classList.contains('hidden')) {
                        debugInfo.classList.remove('hidden');
                        button.textContent = 'Hide Debug Info';

                        // Update debug info
                        const debugHtml = `
                                            <p><strong>Sender:</strong> ${senderSelect.value}</p>
                                            <p><strong>Data Type:</strong> ${dataTypeSelect.value}</p>
                                            <p><strong>Result ID:</strong> ${resultSelect.value}</p>
                                            <p><strong>CSRF Token:</strong> ${document.querySelector('input[name="csrf_token"]').value.substring(0, 10)}...</p>
                                            <p><strong>Form Action:</strong> ${form.action}</p>
                                            <p><strong>Browser:</strong> ${navigator.userAgent}</p>
                                        `;
                        debugInfo.innerHTML = debugHtml;
                    } else {
                        debugInfo.classList.add('hidden');
                        button.textContent = 'Show Debug Info';
                    }
                });

                // Preview toggle
                document.getElementById('togglePreview').addEventListener('click', function () {
                    const preview = document.getElementById('messagePreview');
                    const button = this;

                    if (preview.classList.contains('hidden')) {
                        preview.classList.remove('hidden');
                        button.textContent = 'Hide Preview';

                        // Update preview content
                        if (!resultSelect.value) {
                            preview.textContent = 'Select a result to see preview';
                        } else {
                            const dataType = dataTypeSelect.value;
                            const selectedOption = resultSelect.options[resultSelect.selectedIndex];
                            const previewText = `AIControl Alert\n\n${dataType === 'cctv' ? '*CCTV Detection Alert*' : '*Social Media Alert*'}\nTime: ${new Date().toLocaleString()}\n${selectedOption.textContent}\n\nRecipients will be notified based on their preferences.`;
                            preview.textContent = previewText;
                        }
                    } else {
                        preview.classList.add('hidden');
                        button.textContent = 'Show Preview';
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

                    // Get the test endpoint URL
                    let testEndpoint;
                    if (dataType === 'cctv') {
                        testEndpoint = '{{ route("broadcast.test-results", ["type" => "cctv"]) }}';
                    } else {
                        testEndpoint = '{{ route("broadcast.test-results", ["type" => "social"]) }}';
                    }

                    console.log('Using test endpoint:', testEndpoint);

                    // Fetch data from the test endpoint
                    debugLog(`Fetching data from: ${testEndpoint}`);
                    fetch(testEndpoint)
                        .then(response => {
                            debugLog(`Response status: ${response.status}`);
                            if (!response.ok) {
                                throw new Error(`Network response was not ok: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            debugLog('Received data from endpoint', data);
                            resultSelect.innerHTML = '<option value="">Select a detection result</option>';
                            document.getElementById('loading-results').style.display = 'none';

                            if (data.results && data.results.length > 0) {
                                debugLog(`Processing ${data.results.length} results`);

                                data.results.forEach(result => {
                                    const option = document.createElement('option');
                                    option.value = result.id;

                                    try {
                                        // Format the option text based on data type
                                        if (dataType === 'cctv') {
                                            const date = new Date(result.created_at).toLocaleString();
                                            // Parse the data if it's a string
                                            let resultData = result.data;
                                            if (typeof resultData === 'string') {
                                                try {
                                                    resultData = JSON.parse(resultData);
                                                    debugLog('Parsed JSON data');
                                                } catch (e) {
                                                    debugLog('Failed to parse JSON data', e);
                                                    resultData = {};
                                                }
                                            }

                                            const detectionType = resultData?.detection_type || 'Unknown';
                                            const confidence = resultData?.confidence || 0;
                                            option.textContent = `${date} - ${detectionType} (${Math.round(confidence * 100)}%)`;

                                        } else {
                                            const date = new Date(result.created_at).toLocaleString();
                                            // Parse the data if it's a string
                                            let resultData = result.data;
                                            if (typeof resultData === 'string') {
                                                try {
                                                    resultData = JSON.parse(resultData);
                                                    debugLog('Parsed JSON data');
                                                } catch (e) {
                                                    debugLog('Failed to parse JSON data', e);
                                                    resultData = {};
                                                }
                                            }

                                            const platform = resultData?.platform || 'Unknown';
                                            const accountName = resultData?.account_name || 'Unknown';
                                            option.textContent = `${date} - ${platform}: ${accountName}`;
                                        }

                                        debugLog(`Created option: ${option.textContent}`);
                                    } catch (e) {
                                        debugLog('Error formatting option text', e);
                                        option.textContent = `Result #${result.id} (error formatting)`;
                                    }

                                    resultSelect.appendChild(option);
                                });

                                document.getElementById('no-results').style.display = 'none';
                                debugLog('All results processed and added to dropdown');
                            } else {
                                document.getElementById('no-results').style.display = 'block';
                                const option = document.createElement('option');
                                option.value = "";
                                option.textContent = "No detection results found";
                                resultSelect.appendChild(option);
                                debugLog('No results found');
                            }

                            checkFormValidity();
                        })
                        .catch(error => {
                            debugLog('Error fetching results', error);
                            document.getElementById('loading-results').style.display = 'none';
                            document.getElementById('no-results').style.display = 'block';
                            document.getElementById('no-results').textContent = 'Error loading results. Please try again.';
                            resultSelect.innerHTML = '<option value="">Error loading results</option>';
                            checkFormValidity();
                        });
                });
            });
        </script>
    @endpush

@endsection