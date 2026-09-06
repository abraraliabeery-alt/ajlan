<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Lease;
use App\Models\LeasePayment;
use App\Models\Property;
use App\Models\PropertyUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class LeaseController extends Controller
{
    public function index(Request $request)
    {
        $leases = Lease::query()
            ->with(['customer', 'unit.property', 'payments'])
            ->when($request->q, function ($query, $q) {
                $query->where('contract_no', 'like', "%{$q}%")
                    ->orWhereHas('customer', fn ($c) => $c->where('name', 'like', "%{$q}%")->orWhere('phone', 'like', "%{$q}%"))
                    ->orWhereHas('unit', fn ($u) => $u->where('code', 'like', "%{$q}%"));
            })
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->overdue, fn ($q) => $q->whereHas('payments', fn ($p) => $p->where('due_date', '<', now())->whereRaw('paid_amount < amount')))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $kpis = [
            'active' => Lease::active()->count(),
            'expiring' => Lease::expiringWithin(60)->count(),
            'expected' => (float) LeasePayment::whereHas('lease', fn ($l) => $l->active())->sum('amount'),
            'collected' => (float) LeasePayment::sum('paid_amount'),
            'overdue' => (float) LeasePayment::where('due_date', '<', now())->whereRaw('paid_amount < amount')->selectRaw('SUM(amount - paid_amount) as v')->value('v'),
        ];

        return view('admin.leases.index', compact('leases', 'kpis'));
    }

    public function create(Request $request)
    {
        $customers = Customer::orderBy('name')->get();
        $units = PropertyUnit::with('property')->orderBy('code')->get()
            ->sortBy(fn ($u) => $u->property->code.'/'.$u->code)->values();
        $unit = $request->unit_id ? $units->firstWhere('id', (int) $request->unit_id) : null;

        return view('admin.leases.form', [
            'lease' => new Lease(['start_date' => now()->toDateString(), 'payment_frequency' => 'annual']),
            'customers' => $customers,
            'units' => $units,
            'unit' => $unit,
            'contractNo' => Lease::nextContractNo(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        $this->assertNoOverlap($validated['property_unit_id'], $validated['start_date'], $validated['end_date']);

        $lease = DB::transaction(function () use ($validated) {
            $lease = Lease::create($validated + ['contract_no' => Lease::nextContractNo()]);
            $lease->generateSchedule();
            $lease->syncUnitStatus();

            return $lease;
        });

        return redirect()->route('admin.leases.show', $lease)->with('success', __('admin.saved'));
    }

    public function show(Lease $lease)
    {
        $lease->load(['customer', 'unit.property', 'payments']);

        return view('admin.leases.show', compact('lease'));
    }

    public function edit(Lease $lease)
    {
        $lease->load('payments');
        $customers = Customer::orderBy('name')->get();
        $units = PropertyUnit::with('property')->orderBy('code')->get();

        return view('admin.leases.form', [
            'lease' => $lease,
            'customers' => $customers,
            'units' => $units,
            'unit' => $lease->unit,
            'contractNo' => $lease->contract_no,
        ]);
    }

    public function update(Request $request, Lease $lease)
    {
        $validated = $this->validated($request, $lease);

        if ($lease->start_date->toDateString() !== $validated['start_date']
            || $lease->end_date->toDateString() !== $validated['end_date']) {
            $this->assertNoOverlap($validated['property_unit_id'], $validated['start_date'], $validated['end_date'], $lease->id);
        }

        DB::transaction(function () use ($lease, $validated) {
            $oldUnitId = $lease->property_unit_id;
            $lease->update($validated);
            $lease->generateSchedule();
            $lease->syncUnitStatus();

            if ($oldUnitId !== $lease->property_unit_id) {
                PropertyUnit::find($oldUnitId)?->update(['status' => 'available']);
            }
        });

        return redirect()->route('admin.leases.show', $lease)->with('success', __('admin.saved'));
    }

    public function changeStatus(Request $request, Lease $lease)
    {
        $validated = $request->validate(['status' => ['required', Rule::in(Lease::STATUSES)]]);

        DB::transaction(function () use ($lease, $validated) {
            $lease->update(['status' => $validated['status']]);
            $lease->syncUnitStatus();
        });

        return back()->with('success', __('admin.saved'));
    }

    public function destroy(Lease $lease)
    {
        DB::transaction(function () use ($lease) {
            $lease->delete();
            $lease->syncUnitStatus();
        });

        return redirect()->route('admin.leases.index')->with('success', __('admin.deleted'));
    }

    private function validated(Request $request, ?Lease $lease = null): array
    {
        return $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'property_unit_id' => 'required|exists:property_units,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'annual_rent' => 'required|numeric|min:0',
            'deposit' => 'nullable|numeric|min:0',
            'payment_frequency' => ['required', Rule::in(array_keys(Lease::FREQUENCIES))],
            'status' => ['required', Rule::in(Lease::STATUSES)],
            'notes' => 'nullable|string|max:2000',
        ]);
    }

    private function assertNoOverlap(int $unitId, string $start, string $end, ?int $ignoreId = null): void
    {
        $overlap = Lease::where('property_unit_id', $unitId)
            ->whereIn('status', ['draft', 'active'])
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->where('start_date', '<=', $end)
            ->where('end_date', '>=', $start)
            ->exists();

        if ($overlap) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'property_unit_id' => __('admin.lease_overlap'),
            ]);
        }
    }
}
