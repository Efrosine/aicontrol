@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Edit Recipient</h1>
        </div>

        <div class="bg-white shadow-md rounded-lg p-6">
            <form action="{{ route('broadcast-recipients.update', $broadcastRecipient) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $broadcastRecipient->name) }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="phone_no" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                    <input type="text" name="phone_no" id="phone_no"
                        value="{{ old('phone_no', $broadcastRecipient->phone_no) }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        placeholder="e.g. 6281234567890">
                    @error('phone_no')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4 flex items-center">
                    <input type="checkbox" name="receive_cctv" id="receive_cctv" value="1" {{ old('receive_cctv', $broadcastRecipient->receive_cctv) ? 'checked' : '' }}
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="receive_cctv" class="ml-2 block text-sm text-gray-900">
                        Receive CCTV Detection Results
                    </label>
                </div>

                <div class="mb-4 flex items-center">
                    <input type="checkbox" name="receive_social" id="receive_social" value="1" {{ old('receive_social', $broadcastRecipient->receive_social) ? 'checked' : '' }}
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="receive_social" class="ml-2 block text-sm text-gray-900">
                        Receive Social Media Scraper Results
                    </label>
                </div>

                <div class="flex items-center justify-between mt-6">
                    <a href="{{ route('broadcast-recipients.index') }}" class="text-blue-500 hover:text-blue-700">
                        Cancel
                    </a>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Update Recipient
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection