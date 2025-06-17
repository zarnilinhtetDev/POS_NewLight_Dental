<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MakePayment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [''];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_record');
    }

    public function Id_invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
    // public function GetPrefix()
    // {
    //     return $this->belongsTo(UserProfile::class, 'location', 'branch');
    // }
}
