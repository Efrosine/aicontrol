@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center mb-6">
        <a href="{{ route('dummy-accounts.index') }}" class="btn btn-ghost mr-2">
            &larr;
        </a>
        <h1 class="text-2xl font-bold">Edit Dummy Account</h1>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('dummy-accounts.update', $dummyAccount) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="form-control mb-4">
                    <label class="label" for="username">
                        <span class="label-text">Username</span>
                    </label>
                    <input type="text" name="username" id="username" value="{{ old('username', $dummyAccount->username) }}" required class="input input-bordered @error('username') input-error @enderror">
                    @error('username')
                        <span class="text-error text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-control mb-4">
                    <label class="label" for="password">
                        <span class="label-text">Password (leave blank to keep current)</span>
                    </label>
                    <input type="password" name="password" id="password" class="input input-bordered @error('password') input-error @enderror">
                    @error('password')
                        <span class="text-error text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-control mb-6">
                    <label class="label" for="platform">
                        <span class="label-text">Platform</span>
                    </label>
                    <select name="platform" id="platform" class="select select-bordered @error('platform') select-error @enderror">
                        <option value="ig" {{ old('platform', $dummyAccount->platform) === 'ig' ? 'selected' : '' }}>Instagram</option>
                        <option value="x" {{ old('platform', $dummyAccount->platform) === 'x' ? 'selected' : '' }}>X (Twitter)</option>
                        <option value="twitter" {{ old('platform', $dummyAccount->platform) === 'twitter' ? 'selected' : '' }}>Twitter</option>
                    </select>
                    @error('platform')
                        <span class="text-error text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="btn btn-primary">Update Account</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
