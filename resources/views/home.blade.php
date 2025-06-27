@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold mb-6">Welcome, {{ auth()->user()->username }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="card bg-secondary text-secondary-content">
                    <div class="card-body">
                        <h3 class="card-title">CCTV Cameras</h3>
                        <p>View available CCTV camera feeds</p>
                        <div class="card-actions justify-end">
                            <a href="{{ route('cctv.user-view') }}" class="btn btn-sm">View</a>
                        </div>
                    </div>
                </div>
                <div class="card bg-info text-info-content">
                    <div class="card-body">
                        <h3 class="card-title">Social Media Scraper</h3>
                        <p>Analyze social media accounts using the scraper tool</p>
                        <div class="card-actions justify-end">
                            <a href="{{ route('admin.scraper.form') }}" class="btn btn-sm btn-info">Open Scraper</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection