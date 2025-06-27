@extends('layouts.app')

@section('content')
<div class="hero min-h-screen bg-base-200">
    <div class="hero-content flex-col">
        <div class="text-center">
            <h1 class="text-5xl font-bold">Login</h1>
            <p class="py-6">Welcome to AI Control Panel</p>
        </div>
        <div class="card flex-shrink-0 w-full max-w-sm shadow-2xl bg-base-100">
            <div class="card-body">
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Email</span>
                        </label>
                        <input type="email" name="email" placeholder="email" class="input input-bordered" value="{{ old('email') }}" required />
                        @error('email')
                            <span class="text-error text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Password</span>
                        </label>
                        <input type="password" name="password" placeholder="password" class="input input-bordered" required />
                        @error('password')
                            <span class="text-error text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-control mt-6">
                        <button type="submit" class="btn btn-primary">Login</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
