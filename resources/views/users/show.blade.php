@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex items-center mb-6">
            <a href="{{ route('users.index') }}" class="btn btn-circle btn-outline mr-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <h2 class="text-2xl font-bold">User Details</h2>
        </div>

        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <div class="flex flex-col md:flex-row gap-8">
                    <div class="avatar">
                        <div class="w-24 h-24 rounded-full bg-primary text-primary-content flex items-center justify-center">
                            <span class="text-3xl">{{ substr($user->username, 0, 1) }}</span>
                        </div>
                    </div>
                    
                    <div class="flex-1">
                        <h3 class="text-xl font-bold mb-4">{{ $user->username }}</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-medium">Email</span>
                                </label>
                                <div>{{ $user->email }}</div>
                            </div>
                            
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-medium">Role</span>
                                </label>
                                <div>
                                    <div class="badge {{ $user->role === 'admin' ? 'badge-primary' : 'badge-secondary' }}">
                                        {{ $user->role }}
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-medium">Created At</span>
                                </label>
                                <div>{{ $user->created_at->format('F j, Y') }}</div>
                            </div>
                            
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-medium">Last Updated</span>
                                </label>
                                <div>{{ $user->updated_at->format('F j, Y') }}</div>
                            </div>
                        </div>
                        
                        <div class="flex justify-end mt-6">
                            <div class="join">
                                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-info join-item">Edit User</a>
                                @if(auth()->id() !== $user->id)
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-error join-item">Delete User</button>
                                </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
