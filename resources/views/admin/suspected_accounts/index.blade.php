@extends('layouts.app')

@section('title', 'Suspected Accounts')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Suspected Accounts</h1>
        <a href="{{ route('suspected-accounts.create') }}" class="btn btn-primary">Add New Account</a>
    </div>

    @if(session('success'))
    <div class="alert alert-success mb-4">
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="table w-full">
            <thead>
                <tr>
                    <th class="px-4 py-2 text-left">#</th>
                    <th class="px-4 py-2 text-left">Account Data</th>
                    <th class="px-4 py-2 text-left">Platform</th>
                    <th class="px-4 py-2 text-left">Created At</th>
                    <th class="px-4 py-2 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($suspectedAccounts as $account)
                <tr>
                    <td class="border px-4 py-2">{{ $account->id }}</td>
                    <td class="border px-4 py-2">{{ $account->data }}</td>
                    <td class="border px-4 py-2">
                        @if($account->platform == 'ig')
                            <span class="badge badge-primary">Instagram</span>
                        @elseif($account->platform == 'x')
                            <span class="badge badge-secondary">X</span>
                        @elseif($account->platform == 'twitter')
                            <span class="badge badge-info">Twitter</span>
                        @endif
                    </td>
                    <td class="border px-4 py-2">{{ $account->created_at->format('M d, Y') }}</td>
                    <td class="border px-4 py-2 flex gap-2">
                        <a href="{{ route('suspected-accounts.edit', $account) }}" class="btn btn-sm btn-info">Edit</a>
                        <form action="{{ route('suspected-accounts.destroy', $account) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this account?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-error">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="border px-4 py-2 text-center">No suspected accounts found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $suspectedAccounts->links() }}
    </div>
</div>
@endsection
