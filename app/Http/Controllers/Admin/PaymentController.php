<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeasePayment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $payments = LeasePayment::query()
            ->with('lease.customer', 'lease.unit')
            ->when($request->state, function ($query, $state) {
                match ($state) {
                    'paid' => $query->whereRaw('paid_amount >= amount'),
                    'overdue' => $query->where('due_date', '<', now())->whereRaw('paid_amount < amount'),
                    'partial' => $query->where('paid_amount', '>', 0)->whereRaw('paid_amount < amount')->where('due_date', '>=', now()),
                    default => $query->where('paid_amount', 0)->where('due_date', '>=', now()),
                };
            })
            ->orderBy('due_date')
            ->paginate(25)
            ->withQueryString();

        $totals = [
            'due' => (float) LeasePayment::sum('amount'),
            'collected' => (float) LeasePayment::sum('paid_amount'),
            'overdue' => (float) LeasePayment::where('due_date', '<', now())->whereRaw('paid_amount < amount')->selectRaw('SUM(amount - paid_amount) as v')->value('v'),
        ];

        return view('admin.payments.index', compact('payments', 'totals'));
    }

    public function update(Request $request, LeasePayment $payment)
    {
        $validated = $request->validate([
            'paid_amount' => 'required|numeric|min:0|max:'.$payment->amount,
            'paid_at' => 'nullable|date',
            'method' => 'nullable|string|max:30',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:2000',
        ]);

        $payment->update([
            'paid_amount' => $validated['paid_amount'],
            'paid_at' => $validated['paid_amount'] > 0 ? ($validated['paid_at'] ?? now()->toDateString()) : null,
            'method' => $validated['method'] ?? null,
            'reference' => $validated['reference'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        return back()->with('success', __('admin.saved'));
    }
}
