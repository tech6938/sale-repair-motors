<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;

class VehicleAssign extends Model
{

    protected  $guarded = [];

     public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function preparationManager()
    {
        return $this->belongsTo(User::class, 'preparation_manager_id');
    }

    public function preparationStaff()
    {
        return $this->belongsTo(User::class, 'preparation_staff_id');
    }
}
