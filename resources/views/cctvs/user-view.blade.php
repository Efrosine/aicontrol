@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex items-center mb-6">
                <a href="{{ route('home') }}" class="btn btn-ghost mr-2">
                    &larr;
                </a>
                <h1 class="text-2xl font-bold">CCTV Camera Feeds</h1>
            </div>

            @if($cctvs->isEmpty())
                <div class="alert alert-info">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        class="stroke-current shrink-0 w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>No CCTV cameras available at this time.</span>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($cctvs as $cctv)
                        <div class="card bg-base-100 shadow-xl">
                            <div class="card-body">
                                <h3 class="card-title">{{ $cctv->name }}</h3>
                                <p class="text-sm">Location: {{ $cctv->location }}</p>
                                <div class="my-4 aspect-video bg-base-200 rounded-box flex items-center justify-center">
                                    <div class="text-center">
                                        <div class="loading loading-spinner loading-md mb-2"></div>
                                        <p class="text-sm">Loading stream...</p>
                                    </div>
                                </div>
                                <div class="card-actions justify-end">
                                    <a href="{{ $cctv->stream_url }}" target="_blank" class="btn btn-sm btn-primary">
                                        View Stream
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection