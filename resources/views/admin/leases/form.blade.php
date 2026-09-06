@extends('admin.layout')
@section('title', $lease->exists ? __('admin.edit_lease') : __('admin.new_lease'))
@section('content')
<div class="page-head">
    <h1>{{ $lease->exists ? __('admin.edit_lease') : __('admin.new_lease') }}</h1>
    <span class="muted">{{ $contractNo }}</span>
</div>

<div class="card" style="max-width:820px">
    <form method="post" action="{{ $lease->exists ? route('admin.leases.update', $lease) : route('admin.leases.store') }}">
        @csrf
        @if($lease->exists) @method('PUT') @endif
        <div class="form-grid">
            <div class="field">
                <label>{{ __('admin.customer') }} *</label>
                <select name="customer_id" required>
                    <option value="">—</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}" @selected((string) old('customer_id', $lease->customer_id ?? request('customer_id')) === (string) $c->id)>{{ $c->name }}@if($c->phone) — {{ $c->phone }}@endif</option>
                    @endforeach
                </select>
                <div class="muted" style="margin-top:6px"><a href="{{ route('admin.customers.create') }}" style="color:#8ab8ea">+ {{ __('admin.new_customer') }}</a></div>
            </div>
            <div class="field">
                <label>{{ __('admin.unit') }} *</label>
                <select name="property_unit_id" required>
                    <option value="">—</option>
                    @foreach($units as $u)
                        <option value="{{ $u->id }}" @selected((string) old('property_unit_id', $lease->property_unit_id ?? request('unit_id')) === (string) $u->id)>
                            {{ $u->property?->code }} / {{ $u->code }} ({{ __('admin.st_'.$u->status) }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="field"><label>{{ __('admin.start_date') }} *</label><input type="date" name="start_date" value="{{ old('start_date', $lease->start_date?->toDateString()) }}" required></div>
            <div class="field"><label>{{ __('admin.end_date') }} *</label><input type="date" name="end_date" value="{{ old('end_date', $lease->end_date?->toDateString()) }}" required></div>
            <div class="field"><label>{{ __('admin.annual_rent') }} * ({{ __('admin.sar') }})</label><input type="number" step="0.01" min="0" name="annual_rent" value="{{ old('annual_rent', $lease->annual_rent) }}" required></div>
            <div class="field"><label>{{ __('admin.deposit') }} ({{ __('admin.sar') }})</label><input type="number" step="0.01" min="0" name="deposit" value="{{ old('deposit', $lease->deposit) }}"></div>
            <div class="field">
                <label>{{ __('admin.payment_frequency') }} *</label>
                <select name="payment_frequency" required>
                    @foreach(array_keys(\App\Models\Lease::FREQUENCIES) as $f)
                        <option value="{{ $f }}" @selected(old('payment_frequency', $lease->payment_frequency) === $f)>{{ __('admin.freq_'.$f) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label>{{ __('admin.status') }} *</label>
                <select name="status" required>
                    @foreach(\App\Models\Lease::STATUSES as $s)
                        <option value="{{ $s }}" @selected(old('status', $lease->status ?: 'active') === $s)>{{ __('admin.ls_'.$s) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="field"><label>{{ __('admin.notes') }}</label><textarea name="notes" rows="3">{{ old('notes', $lease->notes) }}</textarea></div>
        <div class="muted" style="margin-bottom:16px">{{ __('admin.schedule_hint') }}</div>
        <button class="btn" type="submit">{{ __('admin.save') }}</button>
        <a class="btn btn-ghost" href="{{ route('admin.leases.index') }}">{{ __('admin.cancel') }}</a>
    </form>
</div>
@endsection
