<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use App\Models\Concerns\Timestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemOption extends Model
{
    use HasFactory, HasUuid, Timestamps;

    protected $fillable = [
        'id',
        'checklist_item_id',
        'uuid',
        'label',
        'value',
    ];

    public function checklistItem()
    {
        return $this->belongsTo(ChecklistItem::class);
    }
}
