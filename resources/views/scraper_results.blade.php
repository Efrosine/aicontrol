@extends('layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto mt-10">
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title mb-4">Scraped Result Details</h2>
                <pre
                    class="mockup-code overflow-x-auto"><code>{{ json_encode(json_decode($result->data), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                <div class="card-actions justify-end mt-4">
                    <a href="#" class="btn btn-primary">Analyze</a>
                    <a href="{{ route('scraper.results.list') }}" class="btn btn-secondary">Back to Results</a>
                </div>
            </div>
        </div>
    </div>
@endsection