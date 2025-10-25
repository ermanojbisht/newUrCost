@extends('layouts.layout001.app')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Schedule of Rates (SORs)</h1>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 glass">
        @forelse ($sors as $sor)
            <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-300 glass">
                <h2 class="text-xl font-bold mb-2 glass">{{ $sor->sorname }}</h2>
                <p class="text-gray-600 mb-4 glass">{{ $sor->display_details }}</p>
                <a href="{{ route('sors.show', $sor) }}" class="text-blue-500 hover:text-blue-700 font-semibold glass">View Items &rarr;</a>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-lg shadow-md p-6">
                <p class="text-gray-600">No SORs found. Data needs to be seeded.</p>
            </div>
        @endforelse
    </div>
@endsection
