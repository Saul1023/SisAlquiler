<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contract extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'room_id',
        'start_date',
        'end_date',
        'payment_frequency',
        'payment_day',
        'base_price',
        'additional_services_price',
        'total_price',
        'services',
        'status',
        'notes',
    ];

    protected $casts = [
        'services' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Get the tenant.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the room.
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get the payments for this contract.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
