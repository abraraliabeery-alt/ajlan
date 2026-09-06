@extends('admin.layout')
@section('title', __('admin.payments'))
@section('content')
<div class="page-head"><h1>{{ __('admin.payments') }}</h1></div>

<div class="kpis">
    <div class="kpi"><div class="l">{{ __('admin.total_expected') }}</div><div class="v">{{ number_format($totals['due'], 0) }}</div></div>
    <div class="kpi accent"><div class="l">{{ __('admin.total_collected') }}</div><div class="v">{{ number_format($totals['collected'], 0) }}</div></div>
    <div class="kpi bad"><div class="l">{{ __('admin.overdue_amount') }}</div><div class="v">{{ number_format($totals['overdue'], 0) }}</div></div>
</div>

<div class="toolbar">
    <form method="get" style="display:flex;gap:8px;flex-wrap:wrap">
        @foreach(['pending','partial','overdue','paid'] as $s)
            <a class="btn {{ request('state', 'pending') === $s ? '' : 'btn-ghost' }}" href="{{ route('admin.payments.index', ['state' => $s]) }}">{{ __('admin.ps_'.$s) }}</a>
        @endforeach
    </form>
</div>

<div class="card" style="padding:0">
    <div class="table-scroll"><table>
        <thead>
            <tr>
                <th>{{ __('admin.contract_no') }}</th>
                <th>{{ __('admin.customer') }}</th>
                <th>{{ __('admin.unit') }}</th>
                <th>{{ __('admin.due_date') }}</th>
                <th>{{ __('admin.amount') }}</th>
                <th>{{ __('admin.paid') }}</th>
                <th>{{ __('admin.status') }}</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        @forelse($payments as $p)
            <tr>
                <td><a href="{{ route('admin.leases.show', $p->lease) }}" class="code" style="color:#8ab8ea;text-decoration:none">{{ $p->lease?->contract_no }}</a></td>
                <td>{{ $p->lease?->customer?->name }}</td>
                <td>{{ $p->lease?->unit?->code }}</td>
                <td style="white-space:nowrap">{{ $p->due_date->format('Y-m-d') }}</td>
                <td>{{ number_format($p->amount, 0) }}</td>
                <td>{{ number_format($p->paid_amount, 0) }}</td>
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
        @empty
            <tr><td colspan="8" class="muted" style="text-align:center;padding:30px">{{ __('admin.no_data') }}</td></tr>
        @endforelse
        </tbody>
    </table></div>
</div>

<div class="pagination">{{ $payments->links() }}</div>
@endsection
