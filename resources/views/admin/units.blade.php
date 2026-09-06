@extends('admin.layout')
@section('title', __('admin.units_of').' '.$property->code)
@section('content')
<div class="toolbar">
    <a href="{{ route('admin.index') }}" class="btn btn-ghost">← {{ __('admin.back') }}</a>
    <h1 style="font-size:20px">{{ __('admin.units_of') }} <span style="color:#b8a168">{{ $property->code }}</span> ({{ $property->units->count() }})</h1>
    <div class="spacer"></div>
    <div class="stat">
        <span>{{ __('admin.available') }}: <b id="c-available" style="color:#5fd4ab">{{ $property->units->where('status','available')->count() }}</b></span>
        <span>{{ __('admin.reserved') }}: <b id="c-reserved" style="color:#e8c170">{{ $property->units->where('status','reserved')->count() }}</b></span>
        <span>{{ __('admin.leased') }}: <b id="c-leased" style="color:#e08b7f">{{ $property->units->where('status','leased')->count() }}</b></span>
    </div>
</div>

@if(session('success'))
    <div class="alert-ok">{{ session('success') }}</div>
@endif

<form method="post" action="{{ route('admin.units.update', $property) }}">
    @csrf
    <div class="toolbar">
        <span style="font-size:12px;color:#9aaba6">{{ __('admin.tap_hint') }}:</span>
        <span class="pill p-avail">{{ __('admin.available') }}</span>
        <span class="pill p-res">{{ __('admin.reserved') }}</span>
        <span class="pill p-leased">{{ __('admin.leased') }}</span>
        <div class="spacer"></div>
        <button type="button" class="btn btn-ghost" onclick="setAll('available')">{{ __('admin.all_available') }}</button>
        <button class="btn" type="submit">{{ __('admin.save') }}</button>
    </div>
    <div class="units-grid">
        @foreach($property->units as $unit)
        <div class="unit-cell {{ $unit->status }}" data-id="{{ $unit->id }}" onclick="cycle(this)">
            <span class="num">{{ $unit->unit_number }}</span>
            <span class="st"></span>
            <input type="hidden" name="statuses[{{ $unit->id }}]" value="{{ $unit->status }}">
        </div>
        @endforeach
    </div>
</form>

<script>
const STATUSES = ['available', 'reserved', 'leased'];
const NAMES = {
    available: @json(__('admin.available')),
    reserved: @json(__('admin.reserved')),
    leased: @json(__('admin.leased'))
};

function cycle(cell) {
    const input = cell.querySelector('input');
    const next = STATUSES[(STATUSES.indexOf(input.value) + 1) % STATUSES.length];
    setStatus(cell, next);
}
function setStatus(cell, status) {
    const input = cell.querySelector('input');
    input.value = status;
    cell.className = 'unit-cell ' + status;
    cell.querySelector('.st').textContent = NAMES[status];
    recount();
}
function setAll(status) {
    document.querySelectorAll('.unit-cell').forEach(c => setStatus(c, status));
}
function recount() {
    const counts = { available: 0, reserved: 0, leased: 0 };
    document.querySelectorAll('.unit-cell input').forEach(i => counts[i.value]++);
    document.getElementById('c-available').textContent = counts.available;
    document.getElementById('c-reserved').textContent = counts.reserved;
    document.getElementById('c-leased').textContent = counts.leased;
}
document.querySelectorAll('.unit-cell').forEach(c => c.querySelector('.st').textContent = NAMES[c.querySelector('input').value]);
</script>
@endsection
