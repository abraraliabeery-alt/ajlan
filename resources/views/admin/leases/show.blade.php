@extends('admin.layout')
@section('title', $lease->contract_no)
@section('content')
<div class="page-head">
    <h1>{{ $lease->contract_no }}</h1>
    <span class="pill p-{{ $lease->status }}">{{ __('admin.ls_'.$lease->status) }}</span>
    <div style="flex:1"></div>
    <a class="btn btn-ghost" href="{{ route('admin.leases.edit', $lease) }}">{{ __('admin.edit') }}</a>
    <form method="post" action="{{ route('admin.leases.destroy', $lease) }}" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
        @csrf @method('DELETE')
        <button class="btn btn-danger" type="submit">{{ __('admin.delete') }}</button>
    </form>
</div>

<div class="kpis">
    <div class="kpi"><div class="l">{{ __('admin.total_rent') }}</div><div class="v">{{ number_format($lease->total_rent, 0) }}</div></div>
    <div class="kpi"><div class="l">{{ __('admin.total_billed') }}</div><div class="v">{{ number_format($lease->total_due, 0) }}</div></div>
    <div class="kpi accent"><div class="l">{{ __('admin.total_collected') }}</div><div class="v">{{ number_format($lease->total_paid, 0) }}</div></div>
    <div class="kpi warn"><div class="l">{{ __('admin.outstanding') }}</div><div class="v">{{ number_format($lease->outstanding, 0) }}</div></div>
    <div class="kpi bad"><div class="l">{{ __('admin.overdue_amount') }}</div><div class="v">{{ number_format($lease->overdue_amount, 0) }}</div></div>
</div>

<div class="grid-2" style="margin-bottom:20px">
    <div class="card">
        <h2>{{ __('admin.lease_details') }}</h2>
        <table>
            <tbody>
                <tr><th>{{ __('admin.customer') }}</th><td><a href="{{ route('admin.customers.show', $lease->customer) }}" style="color:#8ab8ea">{{ $lease->customer?->name }}</a></td></tr>
                <tr><th>{{ __('admin.unit') }}</th><td class="code">{{ $lease->unit?->code }}<span class="sub">{{ $lease->unit?->property?->code }}</span></td></tr>
                <tr><th>{{ __('admin.period') }}</th><td>{{ $lease->start_date->format('Y-m-d') }} → {{ $lease->end_date->format('Y-m-d') }} ({{ $lease->months }} {{ __('admin.months') }})</td></tr>
                <tr><th>{{ __('admin.annual_rent') }}</th><td>{{ number_format($lease->annual_rent, 0) }} {{ __('admin.sar') }}</td></tr>
                <tr><th>{{ __('admin.deposit') }}</th><td>{{ $lease->deposit ? number_format($lease->deposit, 0).' '.__('admin.sar') : '—' }}</td></tr>
                <tr><th>{{ __('admin.payment_frequency') }}</th><td>{{ __('admin.freq_'.$lease->payment_frequency) }}</td></tr>
                @if($lease->notes)<tr><th>{{ __('admin.notes') }}</th><td>{{ $lease->notes }}</td></tr>@endif
            </tbody>
        </table>
        <form method="post" action="{{ route('admin.leases.status', $lease) }}" style="display:flex;gap:8px;margin-top:16px;align-items:end">
            @csrf @method('PATCH')
            <div style="flex:1"><label>{{ __('admin.change_status') }}</label>
                <select name="status">
                    @foreach(\App\Models\Lease::STATUSES as $s)
                        <option value="{{ $s }}" @selected($lease->status === $s)>{{ __('admin.ls_'.$s) }}</option>
                    @endforeach
                </select>
            </div>
            <button class="btn" type="submit">{{ __('admin.apply') }}</button>
        </form>
    </div>

    <div class="card">
        <h2>{{ __('admin.payment_schedule') }}</h2>
        <div class="table-scroll"><table>
            <thead><tr><th>#</th><th>{{ __('admin.due_date') }}</th><th>{{ __('admin.amount') }}</th><th>{{ __('admin.paid') }}</th><th>{{ __('admin.status') }}</th><th></th></tr></thead>
            <tbody>
            @foreach($lease->payments as $p)
            <tr>
                <td>{{ $p->installment_no }}</td>
                <td style="white-space:nowrap">{{ $p->due_date->format('Y-m-d') }}</td>
                <td>{{ number_format($p->amount, 0) }}</td>
                <td>{{ number_format($p->paid_amount, 0) }}@if($p->paid_at)<span class="sub">{{ $p->paid_at->format('Y-m-d') }}</span>@endif</td>
                <td><span class="pill p-{{ $p->state }}">{{ __('admin.ps_'.$p->state) }}</span></td>
                <td>
                    <details>
                        <summary class="btn btn-ghost btn-sm" style="list-style:none;cursor:pointer;display:inline-block">{{ __('admin.record') }}</summary>
                        <form method="post" action="{{ route('admin.payments.update', $p) }}" style="margin-top:10px;min-width:220px">
                            @csrf @method('PATCH')
                            <div class="field"><label>{{ __('admin.paid_amount') }}</label><input type="number" step="0.01" min="0" max="{{ $p->amount }}" name="paid_amount" value="{{ $p->paid_amount ?: $p->remaining }}"></div>
                            <div class="field"><label>{{ __('admin.paid_at') }}</label><input type="date" name="paid_at" value="{{ now()->toDateString() }}"></div>
                            <div class="field"><label>{{ __('admin.method') }}</label><input name="method" value="{{ $p->method }}"></div>
                            <div class="field"><label>{{ __('admin.reference') }}</label><input name="reference" value="{{ $p->reference }}"></div>
                            <button class="btn btn-sm" type="submit">{{ __('admin.save') }}</button>
                        </form>
                    </details>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table></div>
    </div>
</div>
@endsection
