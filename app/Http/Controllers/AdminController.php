<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Lease;
use App\Models\LeasePayment;
use App\Models\Parcel;
use App\Models\Property;
use App\Models\PropertyUnit;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function loginForm()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $request->validate(['password' => 'required|string']);

        if (! hash_equals((string) config('app.admin_password'), (string) $request->password)) {
            return back()->withErrors(['password' => __('admin.wrong_password')])->onlyInput('password');
        }

        $request->session()->regenerate();
        $request->session()->put('admin_authed', true);

        return redirect()->route('admin.index');
    }

    public function setLocale(Request $request, string $locale)
    {
        if (in_array($locale, config('app.supported_locales'), true)) {
            $request->session()->put('admin_locale', $locale);
        }

        return back();
    }

    public function logout(Request $request)
    {
        $request->session()->forget('admin_authed');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    public function index(Request $request)
    {
        $properties = Property::query()
            ->withCount([
                'units',
                'units as available_count' => fn ($query) => $query->where('status', 'available'),
                'units as reserved_count' => fn ($query) => $query->where('status', 'reserved'),
                'units as leased_count' => fn ($query) => $query->where('status', 'leased'),
            ])
            ->when($request->q, fn ($query, $q) => $query->where('code', 'like', '%'.$q.'%'))
            ->orderBy('code')
            ->get();

        $unitCounts = PropertyUnit::query()
            ->selectRaw('status, COUNT(*) as c')->groupBy('status')->pluck('c', 'status');
        $totalUnits = (int) $unitCounts->sum();
        $leasedUnits = (int) ($unitCounts['leased'] ?? 0) + (int) ($unitCounts['sold'] ?? 0);

        $stats = [
            'units' => $totalUnits,
            'available' => (int) ($unitCounts['available'] ?? 0),
            'reserved' => (int) ($unitCounts['reserved'] ?? 0) + (int) ($unitCounts['temp_reserved'] ?? 0),
            'leased' => $leasedUnits,
            'occupancy' => $totalUnits ? round($leasedUnits / $totalUnits * 100, 1) : 0,
            'customers' => Customer::count(),
            'active_leases' => Lease::active()->count(),
            'expiring' => Lease::expiringWithin(60)->count(),
            'expected' => (float) LeasePayment::sum('amount'),
            'collected' => (float) LeasePayment::sum('paid_amount'),
            'overdue' => (float) LeasePayment::where('due_date', '<', now())->whereRaw('paid_amount < amount')->selectRaw('SUM(amount - paid_amount) as v')->value('v'),
        ];

        $recentLeases = Lease::with('customer', 'unit.property')->latest()->limit(5)->get();
        $recentPayments = LeasePayment::with('lease.customer')
            ->where('paid_amount', '>', 0)->latest('paid_at')->limit(5)->get();
        $upcomingDue = LeasePayment::with('lease.customer')
            ->whereRaw('paid_amount < amount')->orderBy('due_date')->limit(6)->get();

        return view('admin.index', compact('properties', 'stats', 'recentLeases', 'recentPayments', 'upcomingDue'));
    }

    public function units(Property $property)
    {
        $property->load('units');

        return view('admin.units', compact('property'));
    }

    public function updateUnits(Request $request, Property $property)
    {
        $validated = $request->validate([
            'statuses' => 'required|array',
            'statuses.*' => 'in:'.implode(',', PropertyUnit::STATUSES),
        ]);

        $units = $property->units()->whereIn('id', array_keys($validated['statuses']))->get()->keyBy('id');

        foreach ($validated['statuses'] as $id => $status) {
            if (isset($units[$id]) && $units[$id]->status !== $status) {
                $units[$id]->update(['status' => $status]);
            }
        }

        return back()->with('success', __('admin.saved'));
    }

    public function map()
    {
        $parcels = Parcel::query()->get()->keyBy('parcel_no');
        $properties = Property::query()->with('units')->orderBy('code')->get(['id', 'code']);

        $propsData = [];
        foreach ($properties as $property) {
            $units = [];
            foreach ($property->units as $unit) {
                $units[] = ['id' => $unit->id, 'n' => $unit->unit_number, 'c' => $unit->code, 'g' => $unit->geometry];
            }
            $propsData[$property->id] = ['code' => $property->code, 'units' => $units];
        }

        return view('admin.map', compact('parcels', 'properties', 'propsData'));
    }

    public function saveUnitGeometry(Request $request, PropertyUnit $unit)
    {
        $validated = $request->validate(['geometry' => 'required|array']);
        $unit->update(['geometry' => $validated['geometry']]);

        return response()->json(['ok' => true]);
    }

    public function deleteUnitGeometry(PropertyUnit $unit)
    {
        $unit->update(['geometry' => null]);

        return response()->json(['ok' => true]);
    }

    public function saveParcel(Request $request)
    {
        $validated = $request->validate([
            'parcel_no' => 'required|string|max:30',
            'block_no' => 'nullable|string|max:10',
            'property_id' => 'nullable|exists:properties,id',
            'status' => 'required|in:'.implode(',', Parcel::STATUSES),
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:50',
            'price' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:2000',
        ]);

        $parcel = Parcel::updateOrCreate(['parcel_no' => $validated['parcel_no']], $validated);

        return response()->json(['ok' => true, 'parcel' => $parcel]);
    }

    public function deleteParcel(Parcel $parcel)
    {
        $parcel->delete();

        return response()->json(['ok' => true]);
    }
}
