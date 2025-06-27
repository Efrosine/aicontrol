@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">CCTV Cameras</h1>
            <a href="{{ route('cctvs.create') }}" class="btn btn-primary">
                Add New Camera
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="table w-full">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Location</th>
                        <th>Stream URL</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($cctvs as $cctv)
                        <tr>
                            <td>{{ $cctv->id }}</td>
                            <td>{{ $cctv->name }}</td>
                            <td>{{ $cctv->location }}</td>
                            <td>
                                <a href="{{ $cctv->stream_url }}" target="_blank" class="link link-primary">
                                    View Stream
                                </a>
                            </td>
                            <td class="flex gap-2">
                                <a href="{{ route('cctvs.show', $cctv) }}" class="btn btn-sm">
                                    View
                                </a>
                                <a href="{{ route('cctvs.edit', $cctv) }}" class="btn btn-sm">
                                    Edit
                                </a>

                                <form action="{{ route('cctvs.destroy', $cctv) }}" method="POST"
                                    onsubmit="return confirm('Are you sure you want to delete this camera?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-error">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">No CCTV cameras found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $cctvs->links() }}
        </div>
    </div>
@endsection