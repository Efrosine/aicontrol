@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <h2 class="text-2xl font-bold mb-6">Welcome, {{ auth()->user()->username }}</h2>
        
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h3 class="card-title">User Dashboard</h3>
                <p>This is your personal dashboard. More features will be available soon.</p>
            </div>
        </div>
    </div>
</div>
@endsection
