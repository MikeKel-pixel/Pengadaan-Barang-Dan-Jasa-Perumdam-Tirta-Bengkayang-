<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorQuote extends Model
{
    use HasFactory;

    protected $fillable = [
        'procurement_request_id',
        'supplier_id',
        'total_penawaran',
        'keterangan',
        'status_terpilih'
    ];

    protected $casts = [
        'total_penawaran' => 'decimal:2',
        'status_terpilih' => 'boolean'
    ];

    public function procurementRequest()
    {
        return $this->belongsTo(ProcurementRequest::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}