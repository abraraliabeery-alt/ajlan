<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Lease extends Model
{
    public const STATUSES = ['draft', 'active', 'expired', 'terminated'];

    /** Installments per year for each frequency. */
    public const FREQUENCIES = ['annual' => 1, 'semi' => 2, 'quarterly' => 4, 'monthly' => 12];

    protected $fillable = [
        'contract_no', 'customer_id', 'property_unit_id', 'start_date', 'end_date',
        'annual_rent', 'deposit', 'payment_frequency', 'status', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'annual_rent' => 'decimal:2',
            'deposit' => 'decimal:2',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(PropertyUnit::class, 'property_unit_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(LeasePayment::class)->orderBy('due_date');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeExpiringWithin(Builder $query, int $days): Builder
    {
        return $query->active()->whereBetween('end_date', [now()->toDateString(), now()->addDays($days)->toDateString()]);
    }

    public function getTotalDueAttribute(): float
    {
        return (float) $this->payments->sum('amount');
    }

    public function getTotalPaidAttribute(): float
    {
        return (float) $this->payments->sum('paid_amount');
    }

    public function getOutstandingAttribute(): float
    {
        return max(0, $this->total_due - $this->total_paid);
    }

    public function getOverdueAmountAttribute(): float
    {
        return (float) $this->payments
            ->filter(fn (LeasePayment $p) => $p->is_overdue)
            ->sum(fn (LeasePayment $p) => $p->remaining);
    }

    public function getMonthsAttribute(): int
    {
        return max(1, (int) round($this->start_date->diffInDays($this->end_date) / 30.4));
    }

    public function getTotalRentAttribute(): float
    {
        return round((float) $this->annual_rent * $this->months / 12, 2);
    }

    /** Rebuild the installment schedule from the contract terms. */
    public function generateSchedule(): void
    {
        $this->payments()->whereNull('paid_at')->where('paid_amount', 0)->delete();

        if ($this->payments()->exists()) {
            return;
        }

        $perYear = self::FREQUENCIES[$this->payment_frequency] ?? 1;
        $monthStep = (int) (12 / $perYear);
        $count = max(1, (int) ceil($this->months / $monthStep));
        $each = round($this->total_rent / $count, 2);

        $rows = [];
        for ($i = 0; $i < $count; $i++) {
            $amount = $i === $count - 1 ? round($this->total_rent - $each * ($count - 1), 2) : $each;
            $rows[] = [
                'installment_no' => $i + 1,
                'due_date' => $this->start_date->copy()->addMonths($i * $monthStep)->toDateString(),
                'amount' => $amount,
                'paid_amount' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        $this->payments()->insert($rows);
    }

    /** Keep the linked unit status in sync with the contract state. */
    public function syncUnitStatus(): void
    {
        $unit = $this->unit;
        if (! $unit) {
            return;
        }

        $unit->update(['status' => match ($this->status) {
            'active' => 'leased',
            'draft' => 'reserved',
            default => $unit->leases()->active()->where('id', '!=', $this->id)->exists() ? 'leased' : 'available',
        }]);
    }

    public static function nextContractNo(): string
    {
        $year = Carbon::now()->year;
        $count = static::whereYear('created_at', $year)->count() + 1;

        return sprintf('AJ-%d-%04d', $year, $count);
    }
}
