@extends('admin.layout')
@section('title', $customer->exists ? __('admin.edit_customer') : __('admin.new_customer'))
@section('content')
<div class="page-head">
    <h1>{{ $customer->exists ? __('admin.edit_customer') : __('admin.new_customer') }}</h1>
</div>

<div class="card" style="max-width:760px">
    <form method="post" action="{{ $customer->exists ? route('admin.customers.update', $customer) : route('admin.customers.store') }}">
        @csrf
        @if($customer->exists) @method('PUT') @endif
        <div class="form-grid">
            <div class="field"><label>{{ __('admin.name') }} *</label><input name="name" value="{{ old('name', $customer->name) }}" required></div>
            <div class="field"><label>{{ __('admin.phone') }}</label><input name="phone" value="{{ old('phone', $customer->phone) }}"></div>
            <div class="field"><label>{{ __('admin.email') }}</label><input type="email" name="email" value="{{ old('email', $customer->email) }}"></div>
            <div class="field"><label>{{ __('admin.company') }}</label><input name="company" value="{{ old('company', $customer->company) }}"></div>
            <div class="field"><label>{{ __('admin.cr_number') }}</label><input name="cr_number" value="{{ old('cr_number', $customer->cr_number) }}"></div>
            <div class="field"><label>{{ __('admin.national_id') }}</label><input name="national_id" value="{{ old('national_id', $customer->national_id) }}"></div>
            <div class="field"><label>{{ __('admin.city') }}</label><input name="city" value="{{ old('city', $customer->city) }}"></div>
        </div>
        <div class="field"><label>{{ __('admin.address') }}</label><textarea name="address" rows="2">{{ old('address', $customer->address) }}</textarea></div>
        <div class="field"><label>{{ __('admin.notes') }}</label><textarea name="notes" rows="3">{{ old('notes', $customer->notes) }}</textarea></div>
        <button class="btn" type="submit">{{ __('admin.save') }}</button>
        <a class="btn btn-ghost" href="{{ route('admin.customers.index') }}">{{ __('admin.cancel') }}</a>
    </form>
</div>
@endsection
