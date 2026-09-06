@extends('admin.layout')
@section('title', __('admin.map_admin'))
@push('head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
    <link rel="stylesheet" href="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.css">
    <style>
        .admin-map-layout{display:grid;grid-template-columns:1fr 330px;gap:18px;align-items:start}
        #admin-map{height:calc(100vh - 140px);min-height:480px;border:1px solid rgba(255,255,255,.12);background:#0a1410}
        .parcel-form label{display:block;font-size:11px;color:#9aaba6;margin:12px 0 4px}
        .parcel-form input,.parcel-form select,.parcel-form textarea{width:100%;background:#0a1a15;border:1px solid rgba(255,255,255,.15);color:#fff;padding:9px 12px;font-family:inherit;font-size:13px}
        .parcel-form textarea{resize:vertical;min-height:60px}
        .form-actions{display:flex;gap:8px;margin-top:16px}
        .form-msg{margin-top:10px;font-size:12px;min-height:18px}
        .form-msg.ok{color:#5fd4ab}.form-msg.err{color:#e08b7f}
        .hint{font-size:12px;color:#9aaba6;margin-bottom:14px}
        .legend-row{display:flex;flex-wrap:wrap;gap:10px;margin-bottom:14px;font-size:11px}
        .legend-row span{display:inline-flex;align-items:center;gap:5px}
        .legend-row i{width:10px;height:10px;display:inline-block;border-radius:2px}
        @media(max-width:900px){.admin-map-layout{grid-template-columns:1fr}#admin-map{height:55vh}}
        .unit-draw{border-top:1px solid rgba(255,255,255,.1);margin-top:18px;padding-top:16px}
        .unit-draw .badge-geom{font-size:10px;padding:2px 8px;border-radius:12px}
        .badge-geom.yes{background:rgba(46,163,127,.2);color:#5fd4ab}
        .badge-geom.no{background:rgba(255,255,255,.08);color:#9aaba6}
    </style>
@endpush
@section('content')
<div class="hint">{{ __('admin.click_parcel_hint') }} — {{ $parcels->count() }} {{ __('admin.managed_parcels') }}</div>
<div class="legend-row">
    @foreach(\App\Models\Parcel::STATUSES as $s)
        <span><i style="background:{{ ['available'=>'#2ea37f','reserved'=>'#d9a441','temp_reserved'=>'#e0782e','leased'=>'#b0554a','visit'=>'#4a90d9'][$s] }}"></i>{{ __('admin.st_'.$s) }}</span>
    @endforeach
</div>
<div class="admin-map-layout">
    <div id="admin-map"></div>
    <div class="card parcel-form">
        <strong style="font-size:15px" id="f-title">{{ __('admin.parcel') }}</strong>
        <form id="parcel-form">
            <input type="hidden" id="f-parcel-id">
            <label>{{ __('admin.parcel') }}</label>
            <input id="f-parcel-no" readonly>
            <label>{{ __('admin.block') }}</label>
            <input id="f-block-no" readonly>
            <label>{{ __('admin.status') }}</label>
            <select id="f-status">
                @foreach(\App\Models\Parcel::STATUSES as $s)
                    <option value="{{ $s }}">{{ __('admin.st_'.$s) }}</option>
                @endforeach
            </select>
            <label>{{ __('admin.linked_property') }}</label>
            <select id="f-property">
                <option value="">{{ __('admin.no_property') }}</option>
                @foreach($properties as $p)
                    <option value="{{ $p->id }}">{{ $p->code }}</option>
                @endforeach
            </select>
            <label>{{ __('admin.customer_name') }}</label>
            <input id="f-customer">
            <label>{{ __('admin.customer_phone') }}</label>
            <input id="f-phone">
            <label>{{ __('admin.price') }}</label>
            <input id="f-price" type="number" step="0.01" min="0">
            <label>{{ __('admin.notes') }}</label>
            <textarea id="f-notes"></textarea>
            <div class="form-actions">
                <button class="btn" type="submit">{{ __('admin.save_parcel') }}</button>
                <button class="btn btn-ghost" type="button" id="f-delete" style="display:none;color:#e08b7f">{{ __('admin.delete_parcel') }}</button>
            </div>
            <div class="form-msg" id="f-msg"></div>
        </form>

        <div class="unit-draw">
            <strong style="font-size:15px">{{ __('admin.draw_unit') }}</strong>
            <div class="hint" style="margin:8px 0">{{ __('admin.draw_hint') }}</div>
            <label>{{ __('admin.linked_property') }}</label>
            <select id="u-property">
                <option value="">{{ __('admin.no_property') }}</option>
                @foreach($properties as $p)
                    <option value="{{ $p->id }}">{{ $p->code }}</option>
                @endforeach
            </select>
            <label>{{ __('admin.unit') }}</label>
            <select id="u-unit"><option value="">—</option></select>
            <div style="margin:8px 0"><span id="u-badge" class="badge-geom no">{{ __('admin.geom_none') }}</span></div>
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
                <input type="checkbox" id="u-buildings" style="width:auto" checked>
                <span>{{ __('admin.show_ai_buildings') }}</span>
            </label>
            <div class="hint" style="margin:6px 0 0">{{ __('admin.ai_buildings_hint') }}</div>
            <div class="form-actions">
                <button class="btn" type="button" id="u-save" disabled>{{ __('admin.save_parcel') }}</button>
                <button class="btn btn-ghost" type="button" id="u-del" disabled style="color:#e08b7f">{{ __('admin.delete_parcel') }}</button>
            </div>
            <div class="form-msg" id="u-msg"></div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.js"></script>
    <script>
    (function () {
        const COLORS = { available: '#2ea37f', reserved: '#d9a441', temp_reserved: '#e0782e', leased: '#b0554a', visit: '#4a90d9' };
        const managed = @json($parcels);
        const csrf = '{{ csrf_token() }}';
        const saveUrl = '{{ route('admin.parcels.save') }}';
        const delBase = '{{ url('/admin/parcels') }}';

        const map = L.map('admin-map');
        L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', { maxZoom: 19 }).addTo(map);
        L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Reference/World_Boundaries_and_Places/MapServer/tile/{z}/{y}/{x}', { maxZoom: 19 }).addTo(map);
        map.setView([24.545, 46.827], 15);

        // --- unit geometry drawing ---
        const propsData = @json($propsData);
        const drawnItems = new L.FeatureGroup().addTo(map);
        const drawControl = new L.Control.Draw({
            draw: { polygon: { shapeOptions: { color: '#ffd75e', weight: 2 } }, marker: false, circle: false, circlemarker: false, polyline: false, rectangle: false },
            edit: { featureGroup: drawnItems }
        });
        map.addControl(drawControl);

        const uProp = document.getElementById('u-property');
        const uUnit = document.getElementById('u-unit');
        const uBadge = document.getElementById('u-badge');
        const uSave = document.getElementById('u-save');
        const uDel = document.getElementById('u-del');
        const uMsg = document.getElementById('u-msg');
        let drawnLayer = null;
        let currentUnit = null;

        const setBadge = has => {
            uBadge.textContent = has ? '{{ __('admin.geom_yes') }}' : '{{ __('admin.geom_none') }}';
            uBadge.className = 'badge-geom ' + (has ? 'yes' : 'no');
            uDel.disabled = !has;
        };

        uProp.addEventListener('change', () => {
            const p = propsData[uProp.value];
            uUnit.innerHTML = '<option value="">—</option>';
            if (p) p.units.forEach(u => {
                const o = document.createElement('option');
                o.value = u.id;
                o.textContent = u.c || ('{{ __('admin.unit') }} ' + u.n);
                o.dataset.geom = u.g ? '1' : '';
                uUnit.appendChild(o);
            });
        });

        uUnit.addEventListener('change', () => {
            drawnItems.clearLayers();
            drawnLayer = null;
            const u = propsData[uProp.value]?.units.find(x => x.id == uUnit.value);
            currentUnit = u || null;
            if (u && u.g) {
                drawnLayer = L.geoJSON(u.g, { style: { color: '#ffd75e', weight: 2, fillOpacity: 0.3 } }).getLayers()[0];
                if (drawnLayer) { drawnItems.addLayer(drawnLayer); map.fitBounds(drawnLayer.getBounds(), { padding: [60, 60] }); }
            }
            setBadge(!!(u && u.g));
            uSave.disabled = !drawnLayer;
        });

        // AI building footprints (Microsoft GlobalMLBuildingFootprints)
        const buildingsLayer = L.geoJSON(null, {
            style: { color: '#4a90d9', weight: 1, fillColor: '#4a90d9', fillOpacity: 0.15 },
            onEachFeature: (f, l) => {
                l.on('mouseover', () => l.setStyle({ weight: 2.5, fillOpacity: 0.4 }));
                l.on('mouseout', () => buildingsLayer.resetStyle(l));
                l.on('click', () => {
                    if (!currentUnit) { uMsg.textContent = '{{ __('admin.pick_unit_first') }}'; uMsg.className = 'form-msg err'; return; }
                    drawnItems.clearLayers();
                    drawnLayer = L.geoJSON(f.geometry, { style: { color: '#ffd75e', weight: 2.5, fillOpacity: 0.4 } }).getLayers()[0];
                    drawnItems.addLayer(drawnLayer);
                    uSave.disabled = false;
                    uMsg.textContent = '{{ __('admin.building_picked') }}';
                    uMsg.className = 'form-msg ok';
                });
            }
        }).addTo(map);
        fetch('/media/buildings.geojson').then(r => r.json()).then(d => buildingsLayer.addData(d));
        document.getElementById('u-buildings').addEventListener('change', e => {
            e.target.checked ? buildingsLayer.addTo(map) : map.removeLayer(buildingsLayer);
        });

        map.on(L.Draw.Event.CREATED, e => {
            if (!currentUnit) { uMsg.textContent = '{{ __('admin.pick_unit_first') }}'; uMsg.className = 'form-msg err'; return; }
            drawnItems.clearLayers();
            drawnLayer = e.layer;
            drawnItems.addLayer(drawnLayer);
            uSave.disabled = false;
        });
        map.on(L.Draw.Event.EDITED, () => { uSave.disabled = !drawnLayer; });
        map.on(L.Draw.Event.DELETED, () => { drawnLayer = null; uSave.disabled = true; });

        uSave.addEventListener('click', () => {
            if (!currentUnit || !drawnLayer) return;
            fetch('{{ url('/admin/units') }}/' + currentUnit.id + '/geometry', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                body: JSON.stringify({ geometry: drawnLayer.toGeoJSON().geometry })
            }).then(r => r.json()).then(res => {
                if (res.ok) { currentUnit.g = drawnLayer.toGeoJSON().geometry; setBadge(true); uMsg.textContent = '{{ __('admin.geom_saved') }}'; uMsg.className = 'form-msg ok'; }
            });
        });

        uDel.addEventListener('click', () => {
            if (!currentUnit) return;
            fetch('{{ url('/admin/units') }}/' + currentUnit.id + '/geometry', {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
            }).then(() => { drawnItems.clearLayers(); drawnLayer = null; currentUnit.g = null; setBadge(false); uSave.disabled = true; });
        });

        const styleOf = p => {
            const m = managed[p.parcel_no];
            return m
                ? { color: '#ffffff', weight: 1.6, fillColor: COLORS[m.status] || '#ff5ec8', fillOpacity: 0.65 }
                : { color: '#ff5ec8', weight: 1, fillOpacity: 0 };
        };

        const msg = (t, ok) => { const m = document.getElementById('f-msg'); m.textContent = t; m.className = 'form-msg ' + (ok ? 'ok' : 'err'); };
        let selected = null;

        fetch('/media/parcels.geojson')
            .then(r => r.json())
            .then(data => {
                const layer = L.geoJSON(data, {
                    style: f => styleOf(f.properties),
                    onEachFeature: (f, l) => {
                        l.on('click', () => {
                            if (selected) selected.setStyle(styleOf(selected.feature.properties));
                            l.setStyle({ color: '#ffd75e', weight: 3, fillOpacity: 0.8 });
                            selected = l;
                            const p = f.properties;
                            const m = managed[p.parcel_no];
                            document.getElementById('f-title').textContent = '{{ __('admin.parcel') }} ' + p.parcel_no;
                            document.getElementById('f-parcel-id').value = m ? m.id : '';
                            document.getElementById('f-parcel-no').value = p.parcel_no;
                            document.getElementById('f-block-no').value = p.block_no;
                            document.getElementById('f-status').value = m ? m.status : 'available';
                            document.getElementById('f-property').value = m && m.property_id ? m.property_id : '';
                            document.getElementById('f-customer').value = m && m.customer_name ? m.customer_name : '';
                            document.getElementById('f-phone').value = m && m.customer_phone ? m.customer_phone : '';
                            document.getElementById('f-price').value = m && m.price ? m.price : '';
                            document.getElementById('f-notes').value = m && m.notes ? m.notes : '';
                            document.getElementById('f-delete').style.display = m ? '' : 'none';
                            msg('');
                        });
                    }
                }).addTo(map);
                map.fitBounds(layer.getBounds(), { padding: [15, 15] });
            });

        document.getElementById('parcel-form').addEventListener('submit', e => {
            e.preventDefault();
            const pno = document.getElementById('f-parcel-no').value;
            if (!pno) return;
            fetch(saveUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                body: JSON.stringify({
                    parcel_no: pno,
                    block_no: document.getElementById('f-block-no').value,
                    property_id: document.getElementById('f-property').value || null,
                    status: document.getElementById('f-status').value,
                    customer_name: document.getElementById('f-customer').value || null,
                    customer_phone: document.getElementById('f-phone').value || null,
                    price: document.getElementById('f-price').value || null,
                    notes: document.getElementById('f-notes').value || null
                })
            })
            .then(r => r.json())
            .then(res => {
                if (!res.ok) { msg('خطأ في الحفظ', false); return; }
                managed[pno] = res.parcel;
                if (selected) selected.setStyle({ color: '#ffffff', weight: 1.6, fillColor: COLORS[res.parcel.status], fillOpacity: 0.65 });
                document.getElementById('f-parcel-id').value = res.parcel.id;
                document.getElementById('f-delete').style.display = '';
                msg('{{ __('admin.parcel_saved') }}', true);
            })
            .catch(() => msg('خطأ في الاتصال', false));
        });

        document.getElementById('f-delete').addEventListener('click', () => {
            const id = document.getElementById('f-parcel-id').value;
            const pno = document.getElementById('f-parcel-no').value;
            if (!id) return;
            fetch(delBase + '/' + id, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
            }).then(() => {
                delete managed[pno];
                if (selected) selected.setStyle(styleOf(selected.feature.properties));
                document.getElementById('f-delete').style.display = 'none';
                msg('{{ __('admin.parcel_deleted') }}', true);
            });
        });
    })();
    </script>
@endpush
