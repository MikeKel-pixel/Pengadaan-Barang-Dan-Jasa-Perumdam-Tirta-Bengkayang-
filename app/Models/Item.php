<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'nama_barang',
        'spesifikasi',
        'satuan',
        'harga_estimasi_default'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function procurementDetails()
    {
        return $this->hasMany(ProcurementDetail::class);
    }
}