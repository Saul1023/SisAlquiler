<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'room_number',
        'floor',
        'capacity',
        'price',
        'status',
        'description',
    ];

    /**
     * Get the contracts for the room.
     */
    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    /**
     * Get the active contract for the room, if any.
     */
    public function activeContract()
    {
        return $this->contracts()->where('status', 'Activo')->first();
    }
}
