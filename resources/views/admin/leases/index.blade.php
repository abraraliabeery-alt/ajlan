@extends('admin.layout')
@section('title', __('admin.leases'))
@section('content')
<div class="page-head">
    <h1>{{ __('admin.leases') }}</h1>
    <div style="flex:1"></div>
    <a class="btn" href="{{ route('admin.leases.create') }}">+ {{ __('admin.new_lease') }}</a>
</div>

<div class="kpis">
    <div class="kpi accent"><div class="l">{{ __('admin.active_leases') }}</div><div class="v">{{ $kpis['active'] }}</div></div>
    <div class="kpi warn"><div class="l">{{ __('admin.expiring_60') }}</div><div class="v">{{ $kpis['expiring'] }}</div></div>
    <div class="kpi"><div class="l">{{ __('admin.total_expected') }}</div><div class="v">{{ number_format($kpis['expected'], 0) }}</div></div>
    <div class="kpi accent"><div class="l">{{ __('admin.total_collected') }}</div><div class="v">{{ number_format($kpis['collected'], 0) }}</div></div>
    <div class="kpi bad"><div class="l">{{ __('admin.overdue_amount') }}</div><div class="v">{{ number_format($kpis['overdue'], 0) }}</div></div>
</div>

<div class="toolbar">
    <form method="get" style="display:flex;gap:8px;flex-wrap:wrap;flex:1">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="{{ __('admin.search_lease') }}" style="max-width:260px">
        <select name="status" style="max-width:150px">
            <option value="">{{ __('admin.all') }}</option>
            @foreach(\App\Models\Lease::STATUSES as $s)
                <option value="{{ $s }}" @selected(request('status') === $s)>{{ __('admin.ls_'.$s) }}</option>
            @endforeach
        </select>
        <label style="display:flex;align-items:center;gap:6px;margin:0;font-size:12px">
            <input type="checkbox" name="overdue" value="1" @checked(request('overdue')) style="width:auto"> {{ __('admin.with_overdue') }}
        </label>
        <button class="btn btn-ghost" type="submit">{{ __('admin.filter') }}</button>
    </form>
</div>

<div class="card" style="padding:0">
    <div class="table-scroll"><table>
        <thead>
            <tr>
                <th>{{ __('admin.contract_no') }}</th>
                <th>{{ __('admin.customer') }}</th>
                <th>{{ __('admin.unit') }}</th>
                <th>{{ __('admin.period') }}</th>
                <th>{{ __('admin.annual_rent') }}</th>
                <th>{{ __('admin.paid') }}</th>
                <th>{{ __('admin.status') }}</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        @forelse($leases as $l)
            <tr>
                <td class="code">{{ $l->contract_no }}</td>
                <td>{{ $l->customer?->name }}<span class="sub">{{ $l->customer?->phone }}</span></td>
                <td>{{ $l->unit?->code }}<span class="sub">{{ $l->unit?->property?->code }}</span></td>
                <td class="muted" style="white-space:nowrap">{{ $l->start_date->format('Y-m-d') }} → {{ $l->end_date->format('Y-m-d') }}</td>
                <td>{{ number_format($l->annual_rent, 0) }}</td>
                <td>{{ number_format($l->total_paid, 0) }} / {{ number_format($l->total_due, 0) }}
                    @if($l->overdue_amount > 0)<span class="sub" style="color:#ff9d9d">{{ number_format($l->overdue_amount, 0) }} {{ __('admin.overdue_short') }}</span>@endif</td>
                <td><span class="pill p-{{ $l->status }}">{{ __('admin.ls_'.$l->status) }}</span></td>
                <td><a class="btn btn-ghost btn-sm" href="{{ route('admin.leases.show', $l) }}">{{ __('admin.view') }}</a></td>
            </tr>
        @empty
            <tr><td colspan="8" class="muted" style="text-align:center;padding:30px">{{ __('admin.no_data') }}</td></tr>
        @endforelse
        </tbody>
    </table></div>
</div>

<div class="pagination">{{ $leases->links() }}</div>
@endsection
