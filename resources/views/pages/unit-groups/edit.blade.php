@extends('layouts.layout001.app')

@section('title', __('Edit Unit Group'))

@section('page-header')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.653-.124-1.28-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.653.124-1.28.356-1.857m0 0a3.001 3.001 0 015.288 0M12 14a4 4 0 100-8 4 4 0 000 8z"></path></svg>
                <span>{{ __('Edit Unit Group') }}</span>
            </h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('Update the details of the unit group.') }}
            </p>
        </div>
    </div>
@endsection

@section('content')
    <div class="card">
        <form action="{{ route('unit-groups.update', $unitGroup) }}" method="POST">
            @csrf
            @method('PUT')
            @include('pages.unit-groups._form', ['units' => $units])
            <div class="flex justify-end space-x-2 mt-6">
                <a href="{{ route('unit-groups.index') }}" class="btn-secondary">{{ __('Cancel') }}</a>
                <button type="submit" class="btn-primary">{{ __('Update') }}</button>
            </div>
        </form>
    </div>
@endsection