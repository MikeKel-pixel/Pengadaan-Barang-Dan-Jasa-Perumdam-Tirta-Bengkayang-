<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcurementLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'procurement_request_id',
        'old_status',
        'new_status',
        'user_id',
        'keterangan'
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