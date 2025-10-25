@extends('layouts.layout001.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-4xl font-bold mb-4">Demo Page</h1>
        <p class="mb-4">This is a demo page to showcase the dark and light theme functionality.</p>

        <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <h2 class="text-2xl font-bold mb-2">Sample Content</h2>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed euismod, nisl nec ultricies lacinia, nisl nisl aliquet nisl, eget aliquam nisl nisl sit amet nisl.</p>
        </div>
    </div>
@endsection
