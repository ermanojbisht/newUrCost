<div class="space-y-6">
    <div>
        <x-form.input label="Lead Distance" name="lead_distance" :value="old('lead_distance', $truckSpeed->lead_distance ?? '')" required />
    </div>
    <div>
        <x-form.input label="Average Speed" name="average_speed" :value="old('average_speed', $truckSpeed->average_speed ?? '')" required />
    </div>
</div>