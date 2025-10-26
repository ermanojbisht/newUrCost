@extends('layouts.layout001.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Show User</h1>
        <a href="{{ route('users.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">Back</a>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
        <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-300 font-bold mb-2">Name:</label>
            <p>{{ $user->name }}</p>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-300 font-bold mb-2">Email:</label>
            <p>{{ $user->email }}</p>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-300 font-bold mb-2">Roles:</label>
            @if(!empty($user->getRoleNames()))
                @foreach($user->getRoleNames() as $v)
                    <span class="inline-block bg-gray-200 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 mr-2 mb-2">{{ $v }}</span>
                @endforeach
            @endif
        </div>
    </div>
</div>
@endsection
