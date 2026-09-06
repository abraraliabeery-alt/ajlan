@extends('admin.layout')
@section('title', __('admin.customers'))
@section('content')
<div class="page-head">
    <h1>{{ __('admin.customers') }}</h1>
    <div style="flex:1"></div>
    <a class="btn" href="{{ route('admin.customers.create') }}">+ {{ __('admin.new_customer') }}</a>
</div>

<div class="toolbar">
    <form method="get" style="display:flex;gap:8px;flex-wrap:wrap;flex:1">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="{{ __('admin.search_customer') }}" style="max-width:300px">
    </form>
</div>

<div class="card" style="padding:0">
    <div class="table-scroll"><table>
        <thead>
            <tr>
                <th>{{ __('admin.name') }}</th>
                <th>{{ __('admin.phone') }}</th>
                <th>{{ __('admin.company') }}</th>
                <th>{{ __('admin.outstanding') }}</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        @forelse($customers as $c)
            <tr>
                <td class="code">{{ $c->name }}</td>
                <td>{{ $c->phone }}@if($c->email)<span class="sub">{{ $c->email }}</span>@endif</td>
                <td>{{ $c->company }}@if($c->cr_number)<span class="sub">CR {{ $c->cr_number }}</span>@endif</td>
                <td>{{ number_format($c->outstanding, 0) }} {{ __('admin.sar') }}</td>
                <td><a class="btn btn-ghost btn-sm" href="{{ route('admin.customers.show', $c) }}">{{ __('admin.view') }}</a></td>
            </tr>
        @empty
            <tr><td colspan="5" class="muted" style="text-align:center;padding:30px">{{ __('admin.no_data') }}</td></tr>
        @endforelse
        </tbody>
    </table></div>
</div>

<div class="pagination">{{ $customers->links() }}</div>
@endsection
