<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use App\Models\Concerns\Timestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory, HasUuid, Timestamps;

    protected $fillable = [
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

    public function inspections()
    {
        return $this->hasMany(Inspection::class);
    }
}
