@extends('layouts.layout001.app')

@section('title', $sor->name . ' - SOR Details')

@section('headstyles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endsection

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">{{ $sor->name }}</h1>

    <div class="card mb-6">
        <h2 class="text-xl font-semibold mb-4">Filter Rates</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <x-form.select name="rate_card" label="Select Rate Card" :options="$rateCards->pluck('name', 'id')" />
            </div>
            <div>
                <x-form.input type="text" name="effective_date" label="Effective Date" value="{{ date('Y-m-d') }}" id="effective_date" />
            </div>
        </div>
    </div>

    <div class="card">
        <h2 class="text-xl font-semibold mb-4">SOR Hierarchy</h2>
        <ul class="space-y-2">
            @foreach ($rootItems as $item)
                <x-sor-item :item="$item" />
            @endforeach
        </ul>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        flatpickr("#effective_date", {});
    </script>
@endpush
