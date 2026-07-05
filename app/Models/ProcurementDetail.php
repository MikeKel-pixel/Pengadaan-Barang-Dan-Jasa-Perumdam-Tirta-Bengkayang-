<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcurementDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'procurement_request_id',
        'item_id',
        'jumlah',
        'harga_estimasi',
        'subtotal'
    ];

    protected $casts = [
        'jumlah' => 'integer',
        'harga_estimasi' => 'decimal:2',
        'subtotal' => 'decimal:2'
    ];

    public function procurementRequest()
    {
        return $this->belongsTo(ProcurementRequest::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}