@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center mb-6">
        <a href="{{ route('dummy-accounts.index') }}" class="btn btn-ghost mr-2">
            &larr;
        </a>
        <h1 class="text-2xl font-bold">Add Dummy Account</h1>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('dummy-accounts.store') }}" method="POST">
                @csrf
                
                <div class="form-control mb-4">
                    <label class="label" for="username">
                        <span class="label-text">Username</span>
                    </label>
                    <input type="text" name="username" id="username" value="{{ old('username') }}" required class="input input-bordered @error('username') input-error @enderror">
                    @error('username')
                        <span class="text-error text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-control mb-4">
                    <label class="label" for="password">
                        <span class="label-text">Password</span>
                    </label>
                    <input type="password" name="password" id="password" required class="input input-bordered @error('password') input-error @enderror">
                    @error('password')
                        <span class="text-error text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-control mb-6">
                    <label class="label" for="platform">
                        <span class="label-text">Platform</span>
                    </label>
                    <select name="platform" id="platform" class="select select-bordered @error('platform') select-error @enderror">
                        <option value="ig" {{ old('platform') === 'ig' ? 'selected' : '' }}>Instagram</option>
                        <option value="x" {{ old('platform') === 'x' ? 'selected' : '' }}>X (Twitter)</option>
                        <option value="twitter" {{ old('platform') === 'twitter' ? 'selected' : '' }}>Twitter</option>
                    </select>
                    @error('platform')
                        <span class="text-error text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="btn btn-primary">Save Account</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
