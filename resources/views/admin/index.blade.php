@extends('admin.layout')
@section('title', __('admin.dashboard'))
@section('content')
<div class="page-head">
    <h1>{{ __('admin.dashboard') }}</h1>
    <div class="spacer" style="flex:1"></div>
    <a class="btn" href="{{ route('admin.leases.create') }}">+ {{ __('admin.new_lease') }}</a>
    <a class="btn btn-ghost" href="{{ route('admin.customers.create') }}">+ {{ __('admin.new_customer') }}</a>
</div>

<div class="kpis">
    <div class="kpi"><div class="l">{{ __('admin.total_units') }}</div><div class="v">{{ $stats['units'] }}</div></div>
    <div class="kpi accent"><div class="l">{{ __('admin.available') }}</div><div class="v">{{ $stats['available'] }}</div></div>
    <div class="kpi warn"><div class="l">{{ __('admin.reserved') }}</div><div class="v">{{ $stats['reserved'] }}</div></div>
    <div class="kpi info"><div class="l">{{ __('admin.leased') }}</div><div class="v">{{ $stats['leased'] }}</div></div>
    <div class="kpi"><div class="l">{{ __('admin.occupancy') }}</div><div class="v">{{ $stats['occupancy'] }}%</div></div>
    <div class="kpi"><div class="l">{{ __('admin.customers') }}</div><div class="v">{{ $stats['customers'] }}</div></div>
    <div class="kpi accent"><div class="l">{{ __('admin.active_leases') }}</div><div class="v">{{ $stats['active_leases'] }}</div></div>
    <div class="kpi warn"><div class="l">{{ __('admin.expiring_60') }}</div><div class="v">{{ $stats['expiring'] }}</div></div>
    <div class="kpi"><div class="l">{{ __('admin.total_expected') }}</div><div class="v">{{ number_format($stats['expected'], 0) }}</div></div>
    <div class="kpi accent"><div class="l">{{ __('admin.total_collected') }}</div><div class="v">{{ number_format($stats['collected'], 0) }}</div></div>
    <div class="kpi bad"><div class="l">{{ __('admin.overdue_amount') }}</div><div class="v">{{ number_format($stats['overdue'], 0) }}</div></div>
</div>

<div class="grid-2" style="margin-bottom:22px">
    <div class="card">
        <h2>{{ __('admin.recent_leases') }}</h2>
        <div class="table-scroll"><table>
            <tbody>
            @forelse($recentLeases as $l)
            <tr>
                <td><a href="{{ route('admin.leases.show', $l) }}" class="code" style="color:#8ab8ea;text-decoration:none">{{ $l->contract_no }}</a>
                    <span class="sub">{{ $l->unit?->code }} — {{ $l->unit?->property?->code }}</span></td>
                <td>{{ $l->customer?->name }}</td>
                <td><span class="pill p-{{ $l->status }}">{{ __('admin.ls_'.$l->status) }}</span></td>
                <td class="muted">{{ $l->start_date->format('Y-m-d') }}</td>
            </tr>
            @empty
            <tr><td class="muted">{{ __('admin.no_data') }}</td></tr>
            @endforelse
            </tbody>
        </table></div>
    </div>
    <div class="card">
        <h2>{{ __('admin.upcoming_due') }}</h2>
        <div class="table-scroll"><table>
            <tbody>
            @forelse($upcomingDue as $p)
            <tr>
                <td>{{ $p->lease?->customer?->name }}<span class="sub">{{ $p->lease?->contract_no }}</span></td>
                <td>{{ number_format($p->remaining, 0) }} {{ __('admin.sar') }}</td>
                <td><span class="pill p-{{ $p->state }}">{{ __('admin.ps_'.$p->state) }}</span></td>
                <td class="muted">{{ $p->due_date->format('Y-m-d') }}</td>
            </tr>
            @empty
            <tr><td class="muted">{{ __('admin.no_data') }}</td></tr>
            @endforelse
            </tbody>
        </table></div>
    </div>
</div>

<div class="toolbar">
    <h1 style="font-size:17px">{{ __('admin.blocks') }}</h1>
    <form method="get" style="width:220px"><input type="search" name="q" value="{{ request('q') }}" placeholder="{{ __('admin.search_code') }}"></form>
</div>
<div class="card" style="padding:0">
    <div class="table-scroll"><table>
        <thead>
            <tr>
                <th>{{ __('admin.code') }}</th>
                <th>{{ __('admin.type') }}</th>
                <th>{{ __('admin.units') }}</th>
                <th>{{ __('admin.available') }}</th>
                <th>{{ __('admin.reserved') }}</th>
                <th>{{ __('admin.leased') }}</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($properties as $property)
            <tr>
                <td class="code">{{ $property->code }}</td>
                <td>{{ __('admin.type_'.$property->type) !== 'admin.type_'.$property->type ? __('admin.type_'.$property->type) : $property->type }}</td>
                <td>{{ $property->units_count }}</td>
                <td><span class="pill p-avail">{{ $property->available_count }}</span></td>
                <td><span class="pill p-res">{{ $property->reserved_count }}</span></td>
                <td><span class="pill p-leased">{{ $property->leased_count }}</span></td>
                <td><a class="btn btn-ghost btn-sm" href="{{ route('admin.units', $property) }}">{{ __('admin.manage_units') }}</a></td>
            </tr>
            @endforeach
        </tbody>
    </table></div>
</div>
@endsection
