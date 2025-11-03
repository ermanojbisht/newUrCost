@extends('layouts.layout001.app')

@section('title', 'SORs')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Schedule of Rates (SORs)</h1>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @foreach ($sors as $sor)
            <div class="card-glass">
                <div class="p-6">
                    <h2 class="text-xl font-bold gradient-text-primary mb-2">{{ $sor->name }}</h2>
                    <p class="text-glass-secondary mb-4">{{ $sor->description }}</p>
                    <div class="flex flex-col space-y-2">
                        <a href="{{ route('sors.show', $sor) }}" class="btn-glass-primary">Chapters & Items</a>
                        <a href="{{ route('sors.admin', $sor) }}" class="btn-glass-primary">Node View</a>
                        <a href="{{ route('sors.datatable', $sor) }}" class="btn-glass-primary">Data Table View</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
