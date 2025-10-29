@extends('layouts.layout001.app')

@section('title', config('app.name', 'Laravel'))
@section('sidebar')
@include('layouts.layout001._partials.sidebar')
@endsection
@section('page-header')
    @isset($header)
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $header }}</h1>
            </div>
        </div>
    @endisset
@endsection

@section('content')
    {{ $slot }}
@endsection
