@extends('admin.layout')
@section('title', $customer->name)
@section('content')
<div class="page-head">
    <h1>{{ $customer->name }}</h1>
    @if($customer->company)<span class="muted">{{ $customer->company }}</span>@endif
    <div style="flex:1"></div>
    <a class="btn" href="{{ route('admin.leases.create', ['customer_id' => $customer->id]) }}">+ {{ __('admin.new_lease') }}</a>
    <a class="btn btn-ghost" href="{{ route('admin.customers.edit', $customer) }}">{{ __('admin.edit') }}</a>
    <form method="post" action="{{ route('admin.customers.destroy', $customer) }}" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
        @csrf @method('DELETE')
        <button class="btn btn-danger" type="submit">{{ __('admin.delete') }}</button>
    </form>
</div>

<div class="kpis">
    <div class="kpi"><div class="l">{{ __('admin.total_billed') }}</div><div class="v">{{ number_format($stats['billed'], 0) }}</div></div>
    <div class="kpi accent"><div class="l">{{ __('admin.total_collected') }}</div><div class="v">{{ number_format($stats['paid'], 0) }}</div></div>
    <div class="kpi warn"><div class="l">{{ __('admin.outstanding') }}</div><div class="v">{{ number_format($stats['outstanding'], 0) }}</div></div>
    <div class="kpi bad"><div class="l">{{ __('admin.overdue_amount') }}</div><div class="v">{{ number_format($stats['overdue'], 0) }}</div></div>
</div>

<div class="grid-2" style="margin-bottom:20px">
    <div class="card">
        <h2>{{ __('admin.contact_info') }}</h2>
        <table>
            <tbody>
                <tr><th>{{ __('admin.phone') }}</th><td>{{ $customer->phone ?: '—' }}</td></tr>
                <tr><th>{{ __('admin.email') }}</th><td>{{ $customer->email ?: '—' }}</td></tr>
                <tr><th>{{ __('admin.company') }}</th><td>{{ $customer->company ?: '—' }}</td></tr>
                <tr><th>{{ __('admin.cr_number') }}</th><td>{{ $customer->cr_number ?: '—' }}</td></tr>
                <tr><th>{{ __('admin.national_id') }}</th><td>{{ $customer->national_id ?: '—' }}</td></tr>
                <tr><th>{{ __('admin.city') }}</th><td>{{ $customer->city ?: '—' }}</td></tr>
                <tr><th>{{ __('admin.address') }}</th><td>{{ $customer->address ?: '—' }}</td></tr>
                @if($customer->notes)<tr><th>{{ __('admin.notes') }}</th><td>{{ $customer->notes }}</td></tr>@endif
            </tbody>
        </table>
    </div>
    <div class="card">
        <h2>{{ __('admin.leases') }} ({{ $customer->leases->count() }})</h2>
        <div class="table-scroll"><table>
            <thead><tr><th>{{ __('admin.contract_no') }}</th><th>{{ __('admin.unit') }}</th><th>{{ __('admin.period') }}</th><th>{{ __('admin.status') }}</th></tr></thead>
            <tbody>
            @forelse($customer->leases as $l)
            <tr>
                <td><a href="{{ route('admin.leases.show', $l) }}" class="code" style="color:#8ab8ea;text-decoration:none">{{ $l->contract_no }}</a></td>
                <td>{{ $l->unit?->code }}<span class="sub">{{ $l->unit?->property?->code }}</span></td>
                <td class="muted">{{ $l->start_date->format('Y-m-d') }} → {{ $l->end_date->format('Y-m-d') }}</td>
                <td><span class="pill p-{{ $l->status }}">{{ __('admin.ls_'.$l->status) }}</span></td>
            </tr>
            @empty
            <tr><td colspan="4" class="muted">{{ __('admin.no_data') }}</td></tr>
            @endforelse
            </tbody>
        </table></div>
    </div>
</div>
@endsection
