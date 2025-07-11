@extends('layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto mt-10">
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title mb-4">Scraped Results</h2>
                <pre
                    class="mockup-code overflow-x-auto"><code>{{ json_encode(json_decode($result->data), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                <div class="card-actions justify-end mt-4">
                    <form action="{{ route('admin.scraper.analyze') }}" method="POST" class="inline">
                        @csrf
                        <input type="hidden" name="id" value="{{ $result->id }}">
                        <input type="hidden" name="text" value="{{ json_encode($result->data) }}">
                        <button type="submit" class="btn btn-primary">Analyze</button>
                    </form>
                    <a href="{{ route('admin.scraper.form') }}" class="btn btn-secondary">Back to Scraper</a>
                </div>
            </div>
        </div>
    </div>
@endsection