@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Dummy Accounts</h1>
        <a href="{{ route('dummy-accounts.create') }}" class="btn btn-primary">
            Add New Account
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
                    <th>Username</th>
                    <th>Platform</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($dummyAccounts as $account)
                    <tr>
                        <td>{{ $account->id }}</td>
                        <td>{{ $account->username }}</td>
                        <td>
                            <span class="badge {{ $account->platform === 'ig' ? 'badge-primary' : ($account->platform === 'x' ? 'badge-secondary' : 'badge-accent') }}">
                                {{ $account->platform }}
                            </span>
                        </td>
                        <td>{{ $account->created_at->format('Y-m-d') }}</td>
                        <td class="flex gap-2">
                            <a href="{{ route('dummy-accounts.edit', $account) }}" class="btn btn-sm">
                                Edit
                            </a>
                            
                            <form action="{{ route('dummy-accounts.destroy', $account) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this account?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-error">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4">No dummy accounts found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
