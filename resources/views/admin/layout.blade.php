<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>@yield('title', __('admin.panel')) | {{ __('site.brand') }}</title>
    <style>
        *{box-sizing:border-box;margin:0}
        body{background:#0a1a15;color:#eef4f1;font-family:Alexandria,'Segoe UI',Tahoma,sans-serif;min-height:100vh;font-size:15px}
        .admin-header{background:#061b16;border-bottom:1px solid rgba(255,255,255,.1);padding:12px 20px;display:flex;align-items:center;gap:16px;position:sticky;top:0;z-index:100}
        .admin-header strong{font-size:15px;white-space:nowrap}
        .admin-header form{margin:0}
        .admin-nav{display:flex;align-items:center;gap:4px;flex:1;flex-wrap:wrap}
        .admin-nav a{color:#9aaba6;font-size:12px;text-decoration:none;padding:7px 11px;border-radius:6px;white-space:nowrap}
        .admin-nav a:hover{color:#fff;background:rgba(255,255,255,.06)}
        .admin-nav a.active{color:#fff;background:rgba(46,163,127,.2)}
        .admin-nav .sp{flex:1}
        .admin-header button{background:none;border:1px solid rgba(255,255,255,.25);color:#fff;padding:7px 14px;font-size:11px;cursor:pointer;font-family:inherit;border-radius:6px}
        .burger{display:none;background:none;border:1px solid rgba(255,255,255,.25);color:#fff;padding:7px 12px;font-size:16px;cursor:pointer;border-radius:6px;font-family:inherit}
        .wrap{max-width:1200px;margin:0 auto;padding:26px 20px}
        .card{background:#0f251e;border:1px solid rgba(255,255,255,.09);padding:22px;border-radius:10px}
        .card h2{font-size:14px;color:#9aaba6;font-weight:500;margin-bottom:14px}
        .table-scroll{overflow-x:auto}
        table{width:100%;border-collapse:collapse;min-width:560px}
        th,td{padding:11px 14px;text-align:start;border-bottom:1px solid rgba(255,255,255,.07);font-size:13px;vertical-align:middle}
        th{color:#9aaba6;font-size:11px;font-weight:400;white-space:nowrap}
        td .sub{display:block;font-size:11px;color:#7d8f89;margin-top:2px}
        .code{font-weight:700}
        .pill{display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;white-space:nowrap}
        .p-avail{background:rgba(46,163,127,.18);color:#5fd4ab}
        .p-res{background:rgba(217,164,65,.18);color:#e8c170}
        .p-leased{background:rgba(176,85,74,.2);color:#e08b7f}
        .p-temp{background:rgba(224,120,46,.18);color:#f0a06a}
        .p-sold{background:rgba(142,111,201,.2);color:#b79ce8}
        .p-visit{background:rgba(74,144,217,.18);color:#8ab8ea}
        .p-paid{background:rgba(46,163,127,.18);color:#5fd4ab}
        .p-pending{background:rgba(255,255,255,.08);color:#9aaba6}
        .p-partial{background:rgba(217,164,65,.18);color:#e8c170}
        .p-overdue{background:rgba(220,60,60,.2);color:#ff9d9d}
        .p-active{background:rgba(46,163,127,.18);color:#5fd4ab}
        .p-draft{background:rgba(255,255,255,.08);color:#9aaba6}
        .p-expired{background:rgba(224,120,46,.18);color:#f0a06a}
        .p-terminated{background:rgba(176,85,74,.2);color:#e08b7f}
        .btn{display:inline-block;background:#075345;color:#fff;border:0;padding:9px 20px;font-size:12px;font-family:inherit;cursor:pointer;text-decoration:none;border-radius:7px}
        .btn:hover{background:#0b745f}
        .btn-ghost{background:transparent;border:1px solid rgba(255,255,255,.2)}
        .btn-sm{padding:6px 12px;font-size:11px}
        .btn-danger{background:rgba(176,85,74,.25);border:1px solid rgba(176,85,74,.5);color:#e08b7f}
        input,select,textarea{background:#0a1a15;border:1px solid rgba(255,255,255,.15);color:#fff;padding:10px 14px;font-family:inherit;font-size:14px;width:100%;border-radius:7px}
        input:focus,select:focus,textarea:focus{outline:none;border-color:#2ea37f}
        label{display:block;font-size:12px;color:#9aaba6;margin-bottom:6px}
        .field{margin-bottom:16px}
        .form-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:0 18px}
        .alert-ok{background:rgba(46,163,127,.15);border:1px solid rgba(46,163,127,.4);color:#5fd4ab;padding:12px 16px;margin-bottom:20px;font-size:13px;border-radius:8px}
        .alert-err{background:rgba(176,85,74,.15);border:1px solid rgba(176,85,74,.4);color:#e08b7f;padding:12px 16px;margin-bottom:20px;font-size:13px;border-radius:8px}
        .err{color:#ff9d9d;font-size:11px;margin-top:4px}
        .units-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(105px,1fr));gap:10px;margin:22px 0}
        .unit-cell{border:1px solid rgba(255,255,255,.12);padding:12px;text-align:center;cursor:pointer;transition:.15s;user-select:none;border-radius:8px}
        .unit-cell .num{font-weight:700;font-size:16px;display:block}
        .unit-cell .st{font-size:10px;color:#9aaba6}
        .unit-cell.available{background:rgba(46,163,127,.15);border-color:#2ea37f}
        .unit-cell.reserved{background:rgba(217,164,65,.15);border-color:#d9a441}
        .unit-cell.leased{background:rgba(176,85,74,.15);border-color:#b0554a}
        .unit-cell:hover{transform:translateY(-2px)}
        .toolbar{display:flex;gap:10px;align-items:center;flex-wrap:wrap;margin-bottom:18px}
        .toolbar .spacer{flex:1}
        .stat{display:flex;gap:14px;font-size:12px;color:#9aaba6;flex-wrap:wrap}
        .stat b{color:#fff}
        .kpis{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:12px;margin-bottom:22px}
        .kpi{background:#0f251e;border:1px solid rgba(255,255,255,.09);border-radius:10px;padding:16px 18px}
        .kpi .v{font-size:22px;font-weight:700;margin-top:6px}
        .kpi .l{font-size:11px;color:#9aaba6}
        .kpi.accent .v{color:#5fd4ab}
        .kpi.warn .v{color:#e8c170}
        .kpi.bad .v{color:#ff9d9d}
        .kpi.info .v{color:#8ab8ea}
        .grid-2{display:grid;grid-template-columns:1fr 1fr;gap:16px}
        .page-head{display:flex;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:20px}
        .page-head h1{font-size:20px}
        .muted{color:#7d8f89;font-size:12px}
        .login-box{max-width:380px;margin:12vh auto}
        .login-box h1{font-size:22px;margin-bottom:22px}
        .login-box .btn{width:100%;margin-top:18px;padding:12px}
        .admin-lang{display:flex;gap:8px}
        .admin-lang a{font-size:10px;color:rgba(255,255,255,.45);letter-spacing:.05em;text-decoration:none;padding:7px 4px}
        .admin-lang a.active,.admin-lang a:hover{color:#fff}
        .pagination{display:flex;gap:6px;flex-wrap:wrap;margin-top:16px}
        .pagination a,.pagination span{padding:6px 12px;border:1px solid rgba(255,255,255,.12);border-radius:6px;font-size:12px;color:#9aaba6;text-decoration:none}
        .pagination .active{background:#075345;color:#fff;border-color:#075345}
        @media(max-width:820px){
            .burger{display:block}
            .admin-nav{display:none;flex-basis:100%;flex-direction:column;align-items:stretch;gap:2px;padding-top:10px}
            .admin-nav.open{display:flex}
            .admin-nav .sp{display:none}
            .grid-2{grid-template-columns:1fr}
            .wrap{padding:18px 12px}
            .card{padding:16px}
        }
    </style>
    @stack('head')
</head>
<body>
    @if(session('admin_authed'))
    @php $r = request()->routeIs; @endphp
    <header class="admin-header">
        <strong>{{ __('site.brand') }} — {{ __('admin.panel') }}</strong>
        <button type="button" class="burger" onclick="document.querySelector('.admin-nav').classList.toggle('open')">☰</button>
        <nav class="admin-nav">
            <a href="{{ route('admin.index') }}" class="{{ request()->routeIs('admin.index','admin.units') ? 'active' : '' }}">{{ __('admin.dashboard') }}</a>
            <a href="{{ route('admin.customers.index') }}" class="{{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">{{ __('admin.customers') }}</a>
            <span class="sp"></span>
            <a href="{{ route('map', app()->getLocale()) }}" target="_blank">{{ __('admin.map') }} ↗</a>
            <a href="{{ route('home', app()->getLocale()) }}" target="_blank">{{ __('admin.site') }} ↗</a>
            <span class="admin-lang">
                @foreach(config('app.supported_locales') as $loc)
                    <a href="{{ route('admin.lang', $loc) }}" class="{{ app()->getLocale() === $loc ? 'active' : '' }}">{{ strtoupper($loc) }}</a>
                @endforeach
            </span>
            <form method="post" action="{{ route('admin.logout') }}">@csrf<button type="submit">{{ __('admin.logout') }}</button></form>
        </nav>
    </header>
    @endif
    <main class="wrap">
        @if(session('success'))<div class="alert-ok">{{ session('success') }}</div>@endif
        @if($errors->any())
            <div class="alert-err">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
        @endif
        @yield('content')
    </main>
    @stack('scripts')
</body>
</html>
