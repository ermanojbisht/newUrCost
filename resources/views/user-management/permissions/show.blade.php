@extends('layouts.layout001.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Show Permission</h1>
        <a href="{{ route('permissions.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">Back</a>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
        <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-300 font-bold mb-2">Name:</label>
            <p>{{ $permission->name }}</p>
        </div>
    </div>
</div>
@endsection
