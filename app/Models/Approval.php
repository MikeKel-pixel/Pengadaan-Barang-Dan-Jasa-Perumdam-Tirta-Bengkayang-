<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Approval extends Model
{
    use HasFactory;

    protected $fillable = [
        'procurement_request_id',
        'user_id',
        'status',
        'catatan',
        'tanggal_approval'
    ];

    protected $casts = [
        'tanggal_approval' => 'datetime'
    ];

    public function procurementRequest()
    {
        return $this->belongsTo(ProcurementRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}