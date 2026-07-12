<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id',
        'amount',
        'payment_date',
        'payment_method',
        'period_covered',
        'receipt_number',
        'status',
        'notes',
    ];

    protected $casts = [
        'payment_date' => 'date',
    ];

    /**
     * Get the contract associated with the payment.
     */
    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    /**
     * Calculate overdue days based on the payment day of the contract.
     * Overdue days are calculated if the payment status is not 'Pagado' or 'Anulado'.
     */
    public function getOverdueDaysAttribute(): int
    {
        if ($this->status === 'Pagado' || $this->status === 'Anulado') {
            return 0;
        }

        // The period_covered has format YYYY-MM
        // Payment is expected by the contract's payment_day of that period's month.
        if (!$this->period_covered || !$this->contract) {
            return 0;
        }

        try {
            $parts = explode('-', $this->period_covered);
            if (count($parts) === 2) {
                $year = intval($parts[0]);
                $month = intval($parts[1]);
                $paymentDay = min($this->contract->payment_day, Carbon::create($year, $month)->daysInMonth);
                
                $dueDate = Carbon::create($year, $month, $paymentDay);
                $today = Carbon::today();

                if ($today->greaterThan($dueDate)) {
                    return $today->diffInDays($dueDate);
                }
            }
        } catch (\Exception $e) {
            // Fallback in case of parse error
        }

        return 0;
    }
}
