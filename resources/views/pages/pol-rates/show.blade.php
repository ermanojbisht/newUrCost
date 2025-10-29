@extends('layouts.layout001.app')

@section('title', __('POL Rate Details'))

@section('page-header')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2v10m0-10h.01M12 6h.01M12 10h.01M12 14h.01M12 18h.01M12 4h.01M12 20h.01M4 12h.01M20 12h.01M4 4h.01M20 4h.01M4 20h.01M20 20h.01M12 8h.01"></path></svg>
                <span>{{ __('POL Rate Details') }}</span>
            </h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('Details of the POL rate.') }}
            </p>
        </div>
    </div>
@endsection

@section('content')
<div class="container mx-auto">
    <div class="card">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-lg font-semibold">{{ __('Rate Date') }}</h3>
                <p>{{ $polRate->rate_date->format('d-m-Y') }}</p>
            </div>
            <div>
                <h3 class="text-lg font-semibold">{{ __('Diesel Rate') }}</h3>
                <p>{{ $polRate->diesel_rate }}</p>
            </div>
            <div>
                <h3 class="text-lg font-semibold">{{ __('Mobile Oil Rate') }}</h3>
                <p>{{ $polRate->mobile_oil_rate }}</p>
            </div>
            <div>
                <h3 class="text-lg font-semibold">{{ __('Laborer Charges') }}</h3>
                <p>{{ $polRate->laborer_charges }}</p>
            </div>
            <div>
                <h3 class="text-lg font-semibold">{{ __('Hiring Charges') }}</h3>
                <p>{{ $polRate->hiring_charges }}</p>
            </div>
            <div>
                <h3 class="text-lg font-semibold">{{ __('Overhead Charges') }}</h3>
                <p>{{ $polRate->overhead_charges }}</p>
            </div>
            <div>
                <h3 class="text-lg font-semibold">{{ __('Mule Rate') }}</h3>
                <p>{{ $polRate->mule_rate }}</p>
            </div>
            <div>
                <h3 class="text-lg font-semibold">{{ __('Valid From') }}</h3>
                <p>{{ $polRate->valid_from->format('d-m-Y') }}</p>
            </div>
            <div>
                <h3 class="text-lg font-semibold">{{ __('Valid To') }}</h3>
                <p>{{ $polRate->valid_to->format('d-m-Y') }}</p>
            </div>
            <div>
                <h3 class="text-lg font-semibold">{{ __('Published At') }}</h3>
                <p>{{ $polRate->published_at->format('d-m-Y H:i') }}</p>
            </div>
            <div>
                <h3 class="text-lg font-semibold">{{ __('Is Locked') }}</h3>
                <p>{{ $polRate->is_locked ? 'Yes' : 'No' }}</p>
            </div>
        </div>
        <div class="flex justify-end space-x-2 mt-6">
            <a href="{{ route('pol-rates.index') }}" class="btn-secondary">{{ __('Back to List') }}</a>
        </div>
    </div>
</div>
@endsection
