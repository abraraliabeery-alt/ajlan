@extends('layouts.app')
@section('title', __('site.map_title').' | '.__('site.brand'))
@section('description', __('site.map_body'))
@push('head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
@endpush
@section('content')
<section class="page-hero compact-hero">
    <div class="container">
        <span class="kicker">{{ __('site.map_kicker') }}</span>
        <h1>{{ __('site.map_title') }}</h1>
        <p>{{ __('site.map_body') }}</p>
    </div>
</section>
<section class="section map-section">
    <div class="container">
        <div class="map-panel">
            <div class="map-panel-col">
                <h3>{{ __('site.legend_title') }}</h3>
                <ul class="legend-items">
                    <li class="legend-group">{{ __('site.legend_units') }}</li>
                    <li><button type="button" data-status="available"><i class="sw" style="background:#2ea37f"></i>{{ __('site.st_available') }}</button></li>
                    <li><button type="button" data-status="reserved"><i class="sw" style="background:#d9a441"></i>{{ __('site.st_reserved') }}</button></li>
                    <li><button type="button" data-status="temp_reserved"><i class="sw" style="background:#e0782e"></i>{{ __('site.st_temp_reserved') }}</button></li>
                    <li><button type="button" data-status="leased"><i class="sw" style="background:#b0554a"></i>{{ __('site.st_leased') }}</button></li>
                    <li><button type="button" data-status="sold"><i class="sw" style="background:#8e6fc9"></i>{{ __('site.st_sold') }}</button></li>
                    <li class="legend-group">{{ __('site.legend_blocks_state') }}</li>
                    <li><span class="legend-info"><i class="sw" style="background:#7ed957"></i>{{ __('site.map_available') }}</span></li>
                    <li><span class="legend-info"><i class="sw" style="background:#ffd166"></i>{{ __('site.map_partial') }}</span></li>
                    <li><span class="legend-info"><i class="sw" style="background:#ef6f6c"></i>{{ __('site.map_unavailable') }}</span></li>
                    <li class="legend-group">{{ __('site.legend_borders') }}</li>
                    <li><button type="button" data-legend-layer="ajlan"><i class="sw ln" style="border-color:#ffffff"></i>{{ __('site.legend_block') }}</button></li>
                    <li><button type="button" data-legend-layer="blocks"><i class="sw ln" style="border-color:#6fc3d9"></i>{{ __('site.legend_other_block') }}</button></li>
                    <li><button type="button" data-legend-layer="parcels"><i class="sw ln" style="border-color:#ff5ec8"></i>{{ __('site.legend_parcel') }}</button></li>
                    <li><button type="button" data-legend-layer="landmarks"><i class="sw" style="background:#00acc1"></i>{{ __('site.legend_landmarks') }}</button></li>
                </ul>
            </div>
            <div class="map-panel-col">
                <h3>{{ __('site.layers_title') }}</h3>
                <ul class="layer-toggles">
                    <li><label><input type="checkbox" data-layer="units" checked><i class="sw" style="background:#2ea37f"></i><span>{{ __('site.layer_units') }}</span></label></li>
                    <li><label><input type="checkbox" data-layer="ajlan" checked><i class="sw ln" style="border-color:#ffffff"></i><span>{{ __('site.layer_ajlan_blocks') }}</span></label></li>
                    <li><label><input type="checkbox" data-layer="blocks" checked><i class="sw ln" style="border-color:#6fc3d9"></i><span>{{ __('site.layer_other_blocks') }}</span></label></li>
                    <li><label><input type="checkbox" data-layer="parcels" checked><i class="sw ln" style="border-color:#ff5ec8"></i><span>{{ __('site.layer_parcels') }}</span></label></li>
                    <li><label><input type="checkbox" data-layer="landmarks" checked><i class="sw" style="background:#00acc1"></i><span>{{ __('site.layer_landmarks') }}</span></label></li>
                </ul>
            </div>
        </div>
        <div class="map-layout">
            <aside class="map-sidebar">
                <div class="map-sidebar-head">{{ __('site.map_blocks') }} <span id="blocks-count"></span></div>
                <div class="map-tools">
                    <input id="map-search" type="search" placeholder="{{ __('site.map_search') }}" autocomplete="off">
                    <div class="map-filters" id="map-filters">
                        <button type="button" data-f="all" class="active">{{ __('site.map_all') }}</button>
                        <button type="button" data-f="available">{{ __('site.map_available') }}</button>
                        <button type="button" data-f="partial">{{ __('site.map_partial') }}</button>
                        <button type="button" data-f="unavailable">{{ __('site.map_unavailable') }}</button>
                    </div>
                    <select id="map-sort" class="map-sort">
                        <option value="ajlan" selected>{{ __('site.sort_ajlan') }}</option>
                        <option value="block">{{ __('site.sort_block') }}</option>
                        <option value="units">{{ __('site.sort_units') }}</option>
                        <option value="available">{{ __('site.sort_available') }}</option>
                        <option value="parcels">{{ __('site.sort_parcels') }}</option>
                    </select>
                </div>
                <ul id="blocks-list" class="blocks-list"></ul>
            </aside>
            <div class="map-wrap">
                <div id="blocks-map" role="application" aria-label="{{ __('site.map_title') }}">
                    <div class="map-status">{{ __('site.map_loading') }}</div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
    (function () {
        const el = document.getElementById('blocks-map');
        const listEl = document.getElementById('blocks-list');
        const searchEl = document.getElementById('map-search');
        const filtersEl = document.getElementById('map-filters');
        const map = L.map(el, { scrollWheelZoom: false, zoomControl: true });
        map.on('focus', () => map.scrollWheelZoom.enable());
        map.on('blur', () => map.scrollWheelZoom.disable());
        map.setView([24.545, 46.827], 15);

        const PARCEL_LINE = '#ff5ec8';
        const STATUS_COLORS = { available: '#7ed957', partial: '#ffd166', unavailable: '#ef6f6c' };
        const PARCEL_COLORS = { available: '#2ea37f', reserved: '#d9a441', temp_reserved: '#e0782e', leased: '#b0554a', sold: '#8e6fc9', visit: '#4a90d9' };
        const PARCEL_STATUS_NAMES = @json($statusNames);
        const parcelStatuses = @json($parcelStatuses);
        const parcelUnits = @json($parcelUnits);
        const unitLayers = {};
        const unitsGroup = L.layerGroup().addTo(map);
        const ajlanBlocksGroup = L.layerGroup().addTo(map);
        const otherBlocksGroup = L.layerGroup().addTo(map);
        const landmarksGroup = L.layerGroup().addTo(map);
        const layerGroups = { units: unitsGroup, ajlan: ajlanBlocksGroup, blocks: otherBlocksGroup, landmarks: landmarksGroup };
        const hiddenStatuses = new Set();
        const unitsByStatus = {};

        const bringUp = () => {
            otherBlocksGroup.eachLayer(l => l.bringToFront());
            ajlanBlocksGroup.eachLayer(l => l.bringToFront());
            unitsGroup.eachLayer(l => l.bringToFront());
        };

        const setLayer = (key, on) => {
            const target = key === 'parcels' ? parcelsLayer : layerGroups[key];
            if (!target) return;
            if (on) { target.addTo(map); bringUp(); } else { map.removeLayer(target); }
        };

        const applyStatus = status => {
            const hidden = hiddenStatuses.has(status);
            (unitsByStatus[status] || []).forEach(l => {
                if (hidden) unitsGroup.removeLayer(l);
                else { unitsGroup.addLayer(l); l.bringToFront(); }
            });
        };

        document.querySelectorAll('[data-layer]').forEach(cb => {
            cb.addEventListener('change', () => {
                setLayer(cb.dataset.layer, cb.checked);
                const btn = document.querySelector('[data-legend-layer="' + cb.dataset.layer + '"]');
                if (btn) btn.classList.toggle('off', !cb.checked);
            });
        });

        document.querySelectorAll('[data-legend-layer]').forEach(btn => {
            btn.addEventListener('click', () => {
                const key = btn.dataset.legendLayer;
                const on = btn.classList.toggle('off') === false;
                setLayer(key, on);
                const cb = document.querySelector('[data-layer="' + key + '"]');
                if (cb) cb.checked = on;
            });
        });

        document.querySelectorAll('[data-status]').forEach(btn => {
            btn.addEventListener('click', () => {
                const status = btn.dataset.status;
                btn.classList.toggle('off') ? hiddenStatuses.add(status) : hiddenStatuses.delete(status);
                applyStatus(status);
            });
        });
        const blockProps = @json($blockProperties);
        const BLOCK_BORDER = '#ffffff';
        const UNIT_BORDER = '#0f1f19';
        const OTHER_BLOCK = '#6fc3d9';
        const baseStyle = { color: OTHER_BLOCK, weight: 1.6, fillColor: OTHER_BLOCK, fillOpacity: 0.04 };
        const hoverStyle = { weight: 3, fillOpacity: 0.55 };
        const activeStyle = { color: '#ffd75e', weight: 3.5, fillOpacity: 0.7 };

        const blockState = prop => {
            if (!prop) return null;
            const total = prop.units || 0;
            const avail = prop.available ?? total;
            if (avail <= 0 && total > 0) return 'unavailable';
            if (avail < total) return 'partial';
            return 'available';
        };
        const styleFor = p => {
            const st = blockState(blockProps[p.block_no]);
            if (!st) return baseStyle;
            return { color: BLOCK_BORDER, weight: 2.2, fillColor: STATUS_COLORS[st], fillOpacity: 0.35 };
        };

        const satellite = L.layerGroup([
            L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', { maxZoom: 19 }),
            L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Reference/World_Boundaries_and_Places/MapServer/tile/{z}/{y}/{x}', { maxZoom: 19 })
        ]);
        const streets = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}', { maxZoom: 19 });
        const planOnly = L.layerGroup();
        satellite.addTo(map);
        L.control.layers({
            '{{ __('site.map_satellite') }}': satellite,
            '{{ __('site.map_streets') }}': streets,
            '{{ __('site.map_plan_only') }}': planOnly
        }, null, { position: 'topright' }).addTo(map);
        L.control.attribution({ prefix: 'Leaflet | Imagery © Esri' }).addTo(map);
        map.attributionControl.setPrefix(false);

        let parcelsLayer = null;
        let parcelsPromise = null;
        const parcelsByBlock = {};
        const parcelLayers = {};
        const ensureParcels = () => {
            if (parcelsPromise) return parcelsPromise;
            parcelsPromise = fetch('/media/parcels.geojson')
                .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
                .then(pdata => {
                    pdata.features.forEach(f => {
                        const b = f.properties.block_no;
                        (parcelsByBlock[b] = parcelsByBlock[b] || []).push(f.properties);
                    });
                    Object.values(parcelsByBlock).forEach(list =>
                        list.sort((a, b) => (parseInt(a.parcel_no, 10) || 0) - (parseInt(b.parcel_no, 10) || 0)));

                    parcelsLayer = L.geoJSON(pdata, {
                        style: () => ({ color: PARCEL_LINE, weight: 1, fillColor: PARCEL_LINE, fillOpacity: 0.05 }),
                        onEachFeature: (f, l) => {
                            const pp = f.properties;
                            const prop = blockProps[pp.block_no];
                            const pst = parcelStatuses[pp.parcel_no];
                            const un = parcelUnits[pp.parcel_no];
                            parcelLayers[pp.parcel_no] = l;
                            l.bindPopup(
                                '<div class="map-popup">' +
                                '<strong>{{ __('site.map_parcel_no') }} ' + (pp.parcel_no ?? '—') + '</strong>' +
                                (un ? '<span class="owned-line">' + un.code + ' · <i style="color:' + (PARCEL_COLORS[un.s] || PARCEL_LINE) + '">●</i> ' + (PARCEL_STATUS_NAMES[un.s] || un.s) + '</span>' : '') +
                                '<span>{{ __('site.map_block') }}: ' + (pp.block_no ?? '—') + '</span>' +
                                (pst ? '<span>{{ __('site.map_status') }}: <i style="color:' + (PARCEL_COLORS[pst] || PARCEL_LINE) + '">●</i> ' + (PARCEL_STATUS_NAMES[pst] || pst) + '</span>' : '') +
                                '<span>{{ __('site.map_area') }}: ' + (pp.area_m2 ? Math.round(pp.area_m2).toLocaleString() + ' {{ __('site.sqm') }}' : '—') + '</span>' +
                                (prop ? '<span class="owned-line">' + prop.code + '</span>' +
                                    (prop.url ? '<a class="popup-link" href="' + prop.url + '">{{ __('site.details') }}</a>' : '') : '') +
                                '</div>'
                            );
                        }
                    });
                    if (document.querySelector('[data-layer="parcels"]').checked) parcelsLayer.addTo(map);
                    bringUp();
                })
                .catch(() => { parcelsPromise = null; });
            return parcelsPromise;
        };

        const LANDMARK_COLORS = {
            mosque: '#00acc1', imam_housing: '#8d6e63', parking: '#90a4ae',
            municipal: '#5c6bc0', plaza: '#ec407a', garden: '#cddc39',
            gas_station: '#ff8a65', electric_station: '#fdd835',
            commercial_center: '#ba68c8', generator: '#78909c',
        };
        const lmColor = t => LANDMARK_COLORS[t] || '#b0bec5';
        const LANDMARK_ICONS = {
            mosque: '🕌', imam_housing: '🏠', parking: '🅿️',
            municipal: '🏛️', plaza: '⛲', garden: '🌳',
            gas_station: '⛽', electric_station: '⚡',
            commercial_center: '🛒', generator: '🔌',
        };
        const lmIcon = p => LANDMARK_ICONS[p.type] || '📍';
        const landmarkAreas = [];
        let landmarkIcons = null;
        const LANDMARK_ZOOM = 17;
        const refreshLandmarkIcons = () => {
            const show = map.getZoom() >= LANDMARK_ZOOM;
            if (landmarkIcons) {
                if (show && !landmarksGroup.hasLayer(landmarkIcons)) landmarksGroup.addLayer(landmarkIcons);
                if (!show && landmarksGroup.hasLayer(landmarkIcons)) landmarksGroup.removeLayer(landmarkIcons);
            }
            landmarkAreas.forEach(l => {
                if (show && !l.getTooltip()) l.bindTooltip(l._lmLabel, { sticky: true, direction: 'top' });
                if (!show && l.getTooltip()) l.unbindTooltip();
            });
        };
        let landmarksPromise = null;
        const ensureLandmarks = () => {
            if (landmarksPromise) return landmarksPromise;
            landmarksPromise = fetch('/landmarks_2705_5.geojson')
                .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
                .then(data => {
                    data.features = (data.features || []).filter(f => f.properties?.type !== 'imam_housing');
                    const iconMarkers = [];
                    const gj = L.geoJSON(data, {
                        style: f => {
                            const c = lmColor(f.properties?.type);
                            return { color: c, weight: 1.6, fillColor: c, fillOpacity: 0.45 };
                        },
                        pointToLayer: (f, latlng) => {
                            const m = L.marker(latlng, {
                                icon: L.divIcon({
                                    className: 'lm-icon',
                                    html: '<span>' + lmIcon(f.properties || {}) + '</span>',
                                    iconSize: [26, 26], iconAnchor: [13, 13],
                                })
                            });
                            iconMarkers.push(m);
                            return m;
                        },
                        onEachFeature: (f, l) => {
                            const p = f.properties || {};
                            const label = p.type_ar || p.type || '';
                            if (f.geometry && f.geometry.type !== 'Point' && p.icon_lat && p.icon_lon) {
                                const mk = L.marker([p.icon_lat, p.icon_lon], {
                                    icon: L.divIcon({
                                        className: 'lm-icon',
                                        html: '<span>' + lmIcon(p) + '</span>',
                                        iconSize: [26, 26], iconAnchor: [13, 13],
                                    }),
                                });
                                mk.bindTooltip(label, { direction: 'top', offset: [0, -12] });
                                iconMarkers.push(mk);
                            }
                            l._lmLabel = label;
                            if (f.geometry && f.geometry.type !== 'Point') {
                                landmarkAreas.push(l);
                                if (map.getZoom() >= LANDMARK_ZOOM) l.bindTooltip(label, { sticky: true, direction: 'top' });
                            } else if (l.bindTooltip) {
                                l.bindTooltip(label, { sticky: true, direction: 'top' });
                            }
                            l.bindPopup(
                                '<div class="map-popup">' +
                                '<strong>' + lmIcon(p) + ' ' + label + '</strong>' +
                                (p.id ? '<span>' + p.id + '</span>' : '') +
                                (p.parcel_no && p.parcel_no !== '0' ? '<span>{{ __('site.map_parcel_no') }}: ' + p.parcel_no + '</span>' : '') +
                                (p.area_m2 ? '<span>{{ __('site.map_area') }}: ' + Math.round(p.area_m2).toLocaleString() + ' {{ __('site.sqm') }}</span>' : '') +
                                '</div>'
                            );
                        }
                    });
                    iconMarkers.forEach(m => gj.removeLayer(m));
                    landmarkIcons = L.layerGroup(iconMarkers);
                    gj.addTo(landmarksGroup);
                    refreshLandmarkIcons();
                    if (document.querySelector('[data-layer="landmarks"]')?.checked) bringUp();
                    else map.removeLayer(landmarksGroup);
                })
                .catch(err => { console.error('[landmarks]', err); landmarksPromise = null; });
            return landmarksPromise;
        };

        const toggleLabels = () => {
            el.classList.toggle('labels-on', map.getZoom() >= 17);
            refreshLandmarkIcons();
        };
        map.on('zoomend', toggleLabels);
        map.on('baselayerchange', e => el.classList.toggle('dark-bg', e.layer === planOnly));

        let blocksLayer = null;
        let activeLayer = null;
        let activeItem = null;
        let currentFilter = 'all';
        const blockLayers = [];

        const UNIT_STATUS_NAMES = { available: @json(__('site.st_available')), reserved: @json(__('site.st_reserved')), leased: @json(__('site.st_leased')) };

        const expandParcels = (item, blockNo) => {
            listEl.querySelectorAll('.parcel-list').forEach(u => u.remove());
            ensureParcels().then(() => {
                const prop = blockProps[blockNo];
                const rows = parcelsByBlock[blockNo] || [];
                if ((!rows.length && !prop) || !item.isConnected) return;
                const ul = document.createElement('ul');
                ul.className = 'parcel-list';
                if (prop && prop.unitList && prop.unitList.length) {
                    const head = document.createElement('li');
                    head.className = 'sub-head';
                    head.textContent = '{{ __('site.warehouses') }}';
                    ul.appendChild(head);
                    prop.unitList.forEach(u => {
                        const uli = document.createElement('li');
                        uli.className = 'unit-row';
                        uli.innerHTML = '<span>' + (u.c || '{{ __('site.unit') }} ' + u.n) + (u.a ? ' · ' + u.a.toLocaleString() + ' {{ __('site.sqm') }}' : '') + '</span>' +
                                        '<span><i class="dot" style="background:' + (PARCEL_COLORS[u.s] || PARCEL_LINE) + '"></i>' + (UNIT_STATUS_NAMES[u.s] || u.s) + '</span>';
                        uli.addEventListener('click', e => {
                            e.stopPropagation();
                            const existing = unitLayers[u.c];
                            if (existing) {
                                map.flyToBounds(existing.getBounds(), { padding: [90, 90], duration: 0.7 });
                                existing.setStyle({ color: '#ffd75e', weight: 3.5 });
                                existing.openPopup();
                                setTimeout(() => existing.setStyle({ color: UNIT_BORDER, weight: 1.2 }), 2500);
                                return;
                            }
                            if (u.p && u.p.length && parcelsLayer) {
                                let bounds = null;
                                u.p.forEach(pn => {
                                    const pl = parcelLayers[pn];
                                    if (pl) bounds = bounds ? bounds.extend(pl.getBounds()) : pl.getBounds();
                                });
                                if (bounds) {
                                    u.p.forEach(pn => {
                                        const pl = parcelLayers[pn];
                                        if (pl) {
                                            pl.setStyle({ color: '#ffd75e', weight: 3, fillOpacity: 0.85 });
                                            setTimeout(() => parcelsLayer.resetStyle(pl), 2500);
                                        }
                                    });
                                    map.flyToBounds(bounds, { padding: [70, 70], duration: 0.7 });
                                    return;
                                }
                            }
                            if (prop.url) window.location = prop.url;
                        });
                        ul.appendChild(uli);
                    });
                    const phead = document.createElement('li');
                    phead.className = 'sub-head';
                    phead.textContent = '{{ __('site.map_parcels_title') }}';
                    ul.appendChild(phead);
                }
                rows.forEach(pp => {
                    const pli = document.createElement('li');
                    pli.innerHTML = '<span>{{ __('site.map_parcel_no') }} ' + pp.parcel_no + '</span>' +
                                    '<span>' + (pp.area_m2 ? Math.round(pp.area_m2).toLocaleString() + ' {{ __('site.sqm') }}' : '—') + '</span>';
                    pli.addEventListener('click', e => {
                        e.stopPropagation();
                        const pl = parcelLayers[pp.parcel_no];
                        if (pl && parcelsLayer) {
                            if (!map.hasLayer(parcelsLayer)) parcelsLayer.addTo(map);
                            map.flyToBounds(pl.getBounds(), { padding: [80, 80], duration: 0.6 });
                            pl.openPopup();
                        }
                    });
                    ul.appendChild(pli);
                });
                item.appendChild(ul);
            });
        };

        const selectBlock = (i, fly = true) => {
            const layer = blockLayers[i];
            if (!layer) return;
            if (activeLayer) { activeLayer.setStyle(styleFor(activeLayer.featureProps)); activeLayer.isActive = false; }
            if (activeItem) activeItem.classList.remove('active');
            layer.setStyle(activeStyle);
            layer.isActive = true;
            layer.bringToFront();
            activeLayer = layer;
            const item = layer._item;
            item.classList.add('active');
            item.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
            activeItem = item;
            expandParcels(item, layer.featureProps.block_no);
            if (fly) map.flyToBounds(layer.getBounds(), { padding: [60, 60], duration: 0.7 });
            layer.openPopup();
        };

        const applyFilters = () => {
            const q = searchEl.value.trim().toLowerCase();
            Array.from(listEl.children).forEach(li => {
                const st = li.dataset.state;
                const okFilter = currentFilter === 'all'
                    || (currentFilter === 'available' && st === 'available')
                    || (currentFilter === 'partial' && st === 'partial')
                    || (currentFilter === 'unavailable' && st === 'unavailable');
                const okSearch = !q || li.dataset.search.includes(q);
                li.style.display = (okFilter && okSearch) ? '' : 'none';
            });
        };
        searchEl.addEventListener('input', applyFilters);
        filtersEl.addEventListener('click', e => {
            const btn = e.target.closest('button');
            if (!btn) return;
            filtersEl.querySelectorAll('button').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            currentFilter = btn.dataset.f;
            applyFilters();
        });

        const sortEl = document.getElementById('map-sort');
        const sorters = {
            block: (a, b) => (+a.dataset.no) - (+b.dataset.no),
            ajlan: (a, b) => (+b.dataset.owned) - (+a.dataset.owned) || (+a.dataset.no) - (+b.dataset.no),
            units: (a, b) => (+b.dataset.units) - (+a.dataset.units) || (+a.dataset.no) - (+b.dataset.no),
            available: (a, b) => (+b.dataset.avail) - (+a.dataset.avail) || (+a.dataset.no) - (+b.dataset.no),
            parcels: (a, b) => (+b.dataset.parcels) - (+a.dataset.parcels) || (+a.dataset.no) - (+b.dataset.no),
        };
        const applySort = () => {
            Array.from(listEl.children)
                .sort(sorters[sortEl?.value] || sorters.ajlan)
                .forEach(li => listEl.appendChild(li));
        };
        sortEl?.addEventListener('change', applySort);

        fetch('/blocks.geojson')
            .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
            .then(data => {
                el.querySelector('.map-status').remove();

                const features = data.features.slice().sort((a, b) =>
                    (parseInt(a.properties.block_no, 10) || 0) - (parseInt(b.properties.block_no, 10) || 0));

                features.forEach((feature, i) => {
                    const p = feature.properties || {};
                    const prop = blockProps[p.block_no];
                    const st = blockState(prop);
                    const layer = L.geoJSON(feature, { style: styleFor(p) });
                    layer.featureProps = p;
                    layer.isAjlan = !!prop;
                    blockLayers.push(layer);
                    (prop ? ajlanBlocksGroup : otherBlocksGroup).addLayer(layer);
                    layer.bindTooltip(String(p.block_no ?? ''), {
                        permanent: true, direction: 'center', className: 'block-label', interactive: false
                    });
                    layer.bindPopup(
                        '<div class="map-popup">' +
                        '<strong>{{ __('site.map_block') }} ' + (p.block_no ?? '—') + '</strong>' +
                        '<span>{{ __('site.map_parcels') }}: ' + (p.parcel_count ?? '—') + '</span>' +
                        '<span>{{ __('site.map_plan') }}: ' + (p.plan_no ?? '—') + '</span>' +
                        (prop ? '<span class="owned-line">' + prop.code + '</span>' +
                            '<span class="status-line">' +
                            '<i style="color:' + STATUS_COLORS.available + '">●</i> ' + (prop.available ?? prop.units) + ' {{ __('site.map_available') }}\u200f' +
                            ' <i style="color:' + STATUS_COLORS.partial + '">●</i> ' + (prop.reserved ?? 0) + ' {{ __('site.map_reserved') }}\u200f' +
                            ' <i style="color:' + STATUS_COLORS.unavailable + '">●</i> ' + (prop.leased ?? 0) + ' {{ __('site.map_leased') }}\u200f' +
                            '</span>' +
                            (prop.url ? '<a class="popup-link" href="' + prop.url + '">{{ __('site.details') }}</a>' : '') : '') +
                        '</div>'
                    );
                    layer.on('click', () => selectBlock(i, false));
                    layer.on('mouseover', () => { if (!layer.isActive) layer.setStyle(hoverStyle); });
                    layer.on('mouseout', () => { if (!layer.isActive) layer.setStyle(styleFor(p)); });

                    const li = document.createElement('li');
                    li.dataset.state = st || 'other';
                    li.dataset.search = ((p.block_no ?? '') + ' ' + (prop ? prop.code : '')).toLowerCase();
                    li.dataset.no = parseInt(p.block_no, 10) || 0;
                    li.dataset.owned = prop ? 1 : 0;
                    li.dataset.units = prop ? (prop.units || 0) : 0;
                    li.dataset.avail = prop ? (prop.available ?? prop.units ?? 0) : 0;
                    li.dataset.parcels = p.parcel_count ?? 0;
                    li._blockIndex = i;
                    layer._item = li;
                    if (prop) li.classList.add('owned');
                    li.innerHTML = '<span class="b-no">' +
                                   (st ? '<i class="dot" style="background:' + STATUS_COLORS[st] + '"></i>' : '') +
                                   '{{ __('site.map_block') }} ' + (p.block_no ?? '—') +
                                   (prop ? ' <em class="b-code">' + prop.code + '</em>' : '') + '</span>' +
                                   '<span class="b-count">' + (prop
                                       ? (prop.available ?? prop.units) + '/' + prop.units + ' {{ __('site.map_available') }}'
                                       : (p.parcel_count ?? '—') + ' {{ __('site.map_parcel') }}') + '</span>';
                    li.addEventListener('click', () => selectBlock(i));
                    listEl.appendChild(li);
                });

                Object.values(blockProps).forEach(prop => (prop.unitList || []).forEach(u => {
                    if (!u.g) return;
                    const ul = L.geoJSON(u.g, { style: { color: UNIT_BORDER, weight: 1.2, fillColor: PARCEL_COLORS[u.s] || PARCEL_LINE, fillOpacity: 0.8 } })
                        .bindPopup('<div class="map-popup"><strong>' + (u.c || '{{ __('site.unit') }} ' + u.n) + '</strong>' +
                            '<span><i style="color:' + (PARCEL_COLORS[u.s] || PARCEL_LINE) + '">●</i> ' + (PARCEL_STATUS_NAMES[u.s] || u.s) + '</span>' +
                            (u.a ? '<span>{{ __('site.map_area') }}: ' + u.a.toLocaleString() + ' {{ __('site.sqm') }}</span>' : '') +
                            (prop.url ? '<a class="popup-link" href="' + prop.url + '">{{ __('site.details') }}</a>' : '') + '</div>')
                        ;
                    unitLayers[u.c] = ul;
                    (unitsByStatus[u.s] = unitsByStatus[u.s] || []).push(ul);
                    if (!hiddenStatuses.has(u.s)) unitsGroup.addLayer(ul);
                }));

                blocksLayer = L.featureGroup(blockLayers);
                applySort();
                document.getElementById('blocks-count').textContent = '· ' + features.length;
                map.fitBounds(blocksLayer.getBounds(), { padding: [20, 20] });
                toggleLabels();
                ensureParcels();
                ensureLandmarks();
            })
            .catch(err => {
                console.error('[map]', err);
                const status = el.querySelector('.map-status');
                if (status) status.textContent = '{{ __('site.map_error') }}';
            });
    })();
    </script>
@endpush
