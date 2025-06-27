@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex items-center mb-6">
            <a href="{{ route('cctvs.index') }}" class="btn btn-ghost mr-2">
                &larr;
            </a>
            <h1 class="text-2xl font-bold">Add New CCTV Camera</h1>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('cctvs.store') }}" method="POST">
                    @csrf

                    <div class="form-control mb-4">
                        <label class="label" for="name">
                            <span class="label-text">Camera Name</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            class="input input-bordered @error('name') input-error @enderror">
                        @error('name')
                            <span class="text-error text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-control mb-4">
                        <label class="label" for="location">
                            <span class="label-text">Location</span>
                        </label>
                        <input type="text" name="location" id="location" value="{{ old('location') }}" required
                            class="input input-bordered @error('location') input-error @enderror">
                        @error('location')
                            <span class="text-error text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-control mb-4">
                        <label class="label" for="origin_url">
                            <span class="label-text">Origin URL</span>
                        </label>
                        <input type="url" name="origin_url" id="origin_url" value="{{ old('origin_url') }}" required
                            class="input input-bordered @error('origin_url') input-error @enderror">
                        @error('origin_url')
                            <span class="text-error text-sm mt-1">{{ $message }}</span>
                        @enderror
                        <span class="label-text-alt mt-1">The original source URL of the camera feed</span>
                    </div>

                    <div class="form-control mb-6">
                        <label class="label" for="stream_url">
                            <span class="label-text">Stream URL</span>
                        </label>
                        <input type="url" name="stream_url" id="stream_url" value="{{ old('stream_url') }}" required
                            class="input input-bordered @error('stream_url') input-error @enderror">
                        @error('stream_url')
                            <span class="text-error text-sm mt-1">{{ $message }}</span>
                        @enderror
                        <span class="label-text-alt mt-1">The URL where the processed stream can be accessed</span>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="btn btn-primary">Add Camera</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection