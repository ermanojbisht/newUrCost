@extends('layouts.layout001.app')

@section('title', __('Edit SOR'))

@section('page-header')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span>{{ __('Edit SOR') }}</span>
            </h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('Update the details of the SOR.') }}
            </p>
        </div>
    </div>
@endsection

@section('content')
<div class="container mx-auto">
    <div class="card">
        <form action="{{ route('sors.update', $sor) }}" method="POST">
            @csrf
            @method('PUT')
            @include('pages.sors._form')
            <div class="flex justify-end space-x-2 mt-6">
                <a href="{{ route('sors.index') }}" class="btn-secondary">{{ __('Cancel') }}</a>
                <button type="submit" class="btn-primary">{{ __('Update') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
