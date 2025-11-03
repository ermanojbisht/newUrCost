@props(['rateCards', 'rateCardId', 'effectiveDate'])

<div class="card mb-6">
    <h2 class="text-xl font-semibold mb-4">Filter Rates</h2>
    <form action="{{ url()->current() }}" method="GET">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <x-form.select name="rate_card_id" label="Select Rate Card" :options="$rateCards->pluck('name', 'id')" :selected="$rateCardId" />
            </div>
            <div>
                <x-form.input type="text" name="effective_date" label="Effective Date" :value="$effectiveDate" id="effective_date" />
            </div>
            <div>
                <button type="submit" class="btn-primary mt-7">Apply Filter</button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        flatpickr("#effective_date", {});
    </script>
@endpush
