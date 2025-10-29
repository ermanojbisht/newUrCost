@extends('layouts.layout001.app')

@section('title', __('Create Rate Card'))

@section('page-header')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H4a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                <span>{{ __('Create Rate Card') }}</span>
            </h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('Add a new rate card to the system.') }}
            </p>
        </div>
    </div>
@endsection

@section('content')
    <div class="card">
        <form action="{{ route('rate-cards.store') }}" method="POST">
            @csrf
            @include('pages.rate-cards._form', ['rateCard' => new \App\Models\Ratecard()])
            <div class="flex justify-end space-x-2 mt-6">
                <a href="{{ route('rate-cards.index') }}" class="btn-secondary">{{ __('Cancel') }}</a>
                <button type="submit" class="btn-primary">{{ __('Save') }}</button>
            </div>
        </form>
    </div>
@endsection