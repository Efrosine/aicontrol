@extends('layouts.app')

@section('title', 'Create Suspected Account')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Create Suspected Account</h1>
        <a href="{{ route('suspected-accounts.index') }}" class="btn btn-neutral">Back to List</a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('suspected-accounts.store') }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label for="data" class="label">Account Data</label>
                <input 
                    type="text" 
                    id="data" 
                    name="data" 
                    class="input input-primary w-full @error('data') input-error @enderror" 
                    value="{{ old('data') }}" 
                    placeholder="Enter suspected account data (e.g., username, email, phone)"
                    required
                >
                @error('data')
                <p class="text-error text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-sm text-gray-500 mt-1">
                    This could be a username, email, phone number, or any identifier for the suspected account.
                </p>
            </div>
            
            <div class="mb-4">
                <label for="platform" class="label">Platform</label>
                <select 
                    id="platform" 
                    name="platform" 
                    class="select select-primary w-full @error('platform') select-error @enderror" 
                    required
                >
                    <option value="" disabled {{ old('platform') ? '' : 'selected' }}>Select Platform</option>
                    <option value="ig" {{ old('platform') == 'ig' ? 'selected' : '' }}>Instagram (ig)</option>
                    <option value="x" {{ old('platform') == 'x' ? 'selected' : '' }}>X (formerly Twitter)</option>
                    <option value="twitter" {{ old('platform') == 'twitter' ? 'selected' : '' }}>Twitter</option>
                </select>
                @error('platform')
                <p class="text-error text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mt-6">
                <button type="submit" class="btn btn-primary">Create Suspected Account</button>
            </div>
        </form>
    </div>
</div>
@endsection
