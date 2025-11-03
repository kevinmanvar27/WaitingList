@extends('admin.layout')

@section('title', 'Admin Login')

@section('content')
<div class="min-h-screen flex items-center justify-center" style="background: var(--color-primary);">
    <div class="max-w-md w-full space-y-8">
        <div>
            <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-white bg-opacity-20">
                <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
            </div>
            <h1 class="mt-6 text-center text-white">
                Admin Login
            </h1>
            <p class="mt-2 text-center text-sm text-white text-opacity-80">
                Access the restaurant management dashboard
            </p>
        </div>
        
        <form class="mt-8 space-y-6" action="{{ route('admin.login') }}" method="POST">
            @csrf
            <div class="rounded-md shadow-sm -space-y-px">
                <div>
                    <label for="email" class="sr-only">Email address</label>
                    <input id="email" 
                           name="email" 
                           type="email" 
                           autocomplete="email" 
                           required 
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-primary focus:border-primary focus:z-10 sm:text-sm @error('email') border-red-500 @enderror"
                           placeholder="Email address"
                           value="{{ old('email') }}">
                    @error('email')
                        <p class="mt-1 text-sm text-red-200">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="password" class="sr-only">Password</label>
                    <input id="password" 
                           name="password" 
                           type="password" 
                           autocomplete="current-password" 
                           required 
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-primary focus:border-primary focus:z-10 sm:text-sm @error('password') border-red-500 @enderror"
                           placeholder="Password">
                    @error('password')
                        <p class="mt-1 text-sm text-red-200">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition duration-150 ease-in-out"
                        style="color: var(--color-primary);">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <svg class="h-5 w-5 group-hover:opacity-80" fill="currentColor" viewBox="0 0 20 20" style="color: var(--color-primary);">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                        </svg>
                    </span>
                    Sign in to Dashboard
                </button>
            </div>

            <div class="text-center">
                <p class="text-sm text-white text-opacity-80">
                    Use your admin credentials to access the dashboard
                </p>
            </div>
        </form>
    </div>
</div>
@endsection
