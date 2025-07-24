<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManagerAssign extends Model
{
    protected $fillable = [
        'manager_id',
        'staff_id',
    ];
}
