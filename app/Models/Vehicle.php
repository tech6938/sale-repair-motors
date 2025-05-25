<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use App\Models\Concerns\Timestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    use HasFactory, HasUuid, Timestamps;

    protected $fillable = [
        'user_by',
        'id',
        'uuid',
        'make',
        'model',
        'year',
        'fuel_type',
        'address',
        'color',
        'price',
        'license_plate',
    ];

    protected $casts = [
        'year' => 'integer',
        'price' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function inspections(): HasMany
    {
        return $this->hasMany(Inspection::class);
    }
}
