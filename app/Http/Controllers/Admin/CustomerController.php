<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $customers = Customer::query()
            ->withCount(['leases', 'leases as active_leases_count' => fn ($q) => $q->active()])
            ->with('leases.payments')
            ->when($request->q, function ($query, $q) {
                $query->where(fn ($w) => $w
                    ->where('name', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('company', 'like', "%{$q}%")
                    ->orWhere('cr_number', 'like', "%{$q}%"));
            })
            ->when($request->status === 'active', fn ($q) => $q->has('leases', '>', 0)->whereHas('leases', fn ($l) => $l->active()))
            ->when($request->status === 'overdue', fn ($q) => $q->whereHas('leases.payments', fn ($p) => $p->where('due_date', '<', now())->whereRaw('paid_amount < amount')))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.customers.index', compact('customers'));
    }

    public function create()
    {
        return view('admin.customers.form', ['customer' => new Customer]);
    }

    public function store(Request $request)
    {
        $customer = Customer::create($this->validated($request));

        return redirect()->route('admin.customers.show', $customer)->with('success', __('admin.saved'));
    }

    public function show(Customer $customer)
    {
        $customer->load(['leases.payments', 'leases.unit.property']);

        $stats = [
            'billed' => $customer->leases->sum('total_due'),
            'paid' => $customer->leases->sum('total_paid'),
            'outstanding' => $customer->outstanding,
            'overdue' => $customer->leases->sum('overdue_amount'),
        ];

        return view('admin.customers.show', compact('customer', 'stats'));
    }

    public function edit(Customer $customer)
    {
        return view('admin.customers.form', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $customer->update($this->validated($request, $customer));

        return redirect()->route('admin.customers.show', $customer)->with('success', __('admin.saved'));
    }

    public function destroy(Customer $customer)
    {
        if ($customer->leases()->exists()) {
            return back()->withErrors(['delete' => __('admin.customer_has_leases')]);
        }

        $customer->delete();

        return redirect()->route('admin.customers.index')->with('success', __('admin.deleted'));
    }

    private function validated(Request $request, ?Customer $customer = null): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:40',
            'email' => ['nullable', 'email', 'max:255', Rule::unique('customers')->ignore($customer)],
            'company' => 'nullable|string|max:255',
            'cr_number' => 'nullable|string|max:40',
            'national_id' => 'nullable|string|max:40',
            'city' => 'nullable|string|max:120',
            'address' => 'nullable|string|max:2000',
            'notes' => 'nullable|string|max:2000',
        ]);
    }
}
