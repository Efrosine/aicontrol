@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex items-center mb-6">
            <a href="{{ route('cctvs.index') }}" class="btn btn-ghost mr-2">
                &larr;
            </a>
            <h1 class="text-2xl font-bold">CCTV Camera Details</h1>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="card bg-base-100">
                <div class="card-body">
                    <h2 class="card-title">Camera Information</h2>

                    <div class="overflow-x-auto">
                        <table class="table w-full">
                            <tbody>
                                <tr>
                                    <td class="font-bold">Name</td>
                                    <td>{{ $cctv->name }}</td>
                                </tr>
                                <tr>
                                    <td class="font-bold">Location</td>
                                    <td>{{ $cctv->location }}</td>
                                </tr>
                                <tr>
                                    <td class="font-bold">Origin URL</td>
                                    <td>
                                        <a href="{{ $cctv->origin_url }}" target="_blank" class="link link-primary">
                                            {{ $cctv->origin_url }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font-bold">Stream URL</td>
                                    <td>
                                        <a href="{{ $cctv->stream_url }}" target="_blank" class="link link-primary">
                                            {{ $cctv->stream_url }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font-bold">Added On</td>
                                    <td>{{ $cctv->created_at->format('F j, Y, g:i a') }}</td>
                                </tr>
                                <tr>
                                    <td class="font-bold">Last Updated</td>
                                    <td>{{ $cctv->updated_at->format('F j, Y, g:i a') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="card-actions justify-end mt-4">
                        <a href="{{ route('cctvs.edit', $cctv) }}" class="btn btn-primary">Edit</a>

                        <form action="{{ route('cctvs.destroy', $cctv) }}" method="POST"
                            onsubmit="return confirm('Are you sure you want to delete this camera?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-error">Delete</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card bg-base-100">
                <div class="card-body">
                    <h2 class="card-title">Live Stream</h2>

                    <div class="aspect-video bg-base-200 rounded-box flex items-center justify-center mb-4">
                        <img src="{{ $cctv->stream_url }}" alt="">
                    </div>

                    <div class="alert alert-info">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            class="stroke-current shrink-0 w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Connect this stream to AI detection by using the detection configuration panel.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection