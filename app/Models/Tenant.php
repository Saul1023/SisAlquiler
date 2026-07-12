<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Tenant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'identity_number',
        'phone',
        'email',
        'photo_path',
        'notes',
        'status',
    ];

    /**
     * Get the contracts for the tenant.
     */
    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    /**
     * Get the profile photo URL.
     */
    public function getPhotoUrlAttribute(): string
    {
        if ($this->photo_path) {
            return Storage::url($this->photo_path);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=3B82F6&color=fff&size=128';
    }
}
