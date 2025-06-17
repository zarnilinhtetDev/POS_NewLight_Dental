<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PoMakePayment extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function po()
    {
        return $this->belongsTo(PurchaseOrder::class, 'po_record');
    }

    public function Id_po()
    {
        return $this->belongsTo(PurchaseOrder::class, 'po_id');
    }
}
