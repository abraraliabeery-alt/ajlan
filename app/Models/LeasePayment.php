<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeasePayment extends Model
{
    protected $fillable = [
        'lease_id', 'installment_no', 'due_date', 'amount',
        'paid_amount', 'paid_at', 'method', 'reference', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'paid_at' => 'date',
            'amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
        ];
    }

    public function lease(): BelongsTo
    {
        return $this->belongsTo(Lease::class);
    }

    public function getRemainingAttribute(): float
    {
        return max(0, (float) $this->amount - (float) $this->paid_amount);
    }

    public function getIsPaidAttribute(): bool
    {
        return $this->remaining <= 0.009;
    }

    public function getIsOverdueAttribute(): bool
    {
        return ! $this->is_paid && $this->due_date->isPast();
    }

    public function getStateAttribute(): string
    {
        if ($this->is_paid) {
            return 'paid';
        }

        if ($this->is_overdue) {
            return 'overdue';
        }

        return (float) $this->paid_amount > 0 ? 'partial' : 'pending';
    }
}
