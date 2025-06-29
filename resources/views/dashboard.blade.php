@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold mb-6">Admin Dashboard</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="card bg-primary text-primary-content">
                    <div class="card-body">
                        <h3 class="card-title">Users</h3>
                        <p>Manage user accounts</p>
                        <div class="card-actions justify-end">
                            <a href="{{ route('users.index') }}" class="btn btn-sm">View</a>
                        </div>
                    </div>
                </div>

                <div class="card bg-secondary text-secondary-content">
                    <div class="card-body">
                        <h3 class="card-title">CCTV</h3>
                        <p>Monitor CCTV detection</p>
                        <div class="card-actions justify-end">
                            <a href="{{ route('cctvs.index') }}" class="btn btn-sm">View</a>
                        </div>
                    </div>
                </div>

                <div class="card bg-neutral text-neutral-content">
                    <div class="card-body">
                        <h3 class="card-title">Dummy Accounts</h3>
                        <p>Manage social media dummy accounts</p>
                        <div class="card-actions justify-end">
                            <a href="{{ route('dummy-accounts.index') }}" class="btn btn-sm">View</a>
                        </div>
                    </div>
                </div>

                <div class="card bg-error text-error-content">
                    <div class="card-body">
                        <h3 class="card-title">Suspected Accounts</h3>
                        <p>Manage suspected accounts database</p>
                        <div class="card-actions justify-end">
                            <a href="{{ route('suspected-accounts.index') }}" class="btn btn-sm">View</a>
                        </div>
                    </div>
                </div>

                <div class="card bg-info text-info-content">
                    <div class="card-body">
                        <h3 class="card-title">Social Media Scraper</h3>
                        <p>Run Instagram/X/Twitter scraping using dummy accounts</p>
                        <div class="card-actions justify-end">
                            <a href="{{ route('admin.scraper.form') }}" class="btn btn-sm btn-info">Open Scraper</a>
                        </div>
                    </div>
                </div>
                <div class="card bg-accent text-accent-content">
                    <div class="card-body">
                        <h3 class="card-title">Sraper Result</h3>
                        <p>View scraped data results</p>
                        <div class="card-actions justify-end">
                            <a href="{{ route('scraper.results.list') }}" class="btn btn-sm">View</a>
                        </div>
                    </div>
                </div>

                <div class="card bg-warning text-warning-content">
                    <div class="card-body">
                        <h3 class="card-title">History Social Media Analyze</h3>
                        <p>View all social media analyze results</p>
                        <div class="card-actions justify-end">
                            <a href="{{ route('admin.social_detection_results.index') }}" class="btn btn-sm">View</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection