@extends('layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto mt-10">
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title mb-4">All Scraped Results</h2>
                <div class="overflow-x-auto">
                    <table class="table table-zebra w-full">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Account</th>
                                <th>URL</th>
                                <th>Created At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($results as $result)
                                <tr>
                                    <td>{{ $result->id }}</td>
                                    <td>{{ $result->account }}</td>
                                    <td><a href="{{ $result->url }}" class="link link-primary" target="_blank">View</a></td>
                                    <td>{{ $result->created_at }}</td>
                                    <td>
                                        <a href="{{ route('admin.scraper.results', $result->id) }}"
                                            class="btn btn-sm btn-info">Details</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection