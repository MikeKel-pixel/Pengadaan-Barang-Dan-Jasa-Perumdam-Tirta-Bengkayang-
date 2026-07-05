<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes; // HAPUS ATAU COMMENT INI

class ProcurementRequest extends Model
{
    use HasFactory;
    // use SoftDeletes; // HAPUS ATAU COMMENT INI

    protected $fillable = [
        'kode_pengajuan',
        'user_id',
        'tanggal_pengajuan',
        'total_estimasi',
        'status',
        'keterangan'
    ];

    protected $casts = [
        'tanggal_pengajuan' => 'date',
        'total_estimasi' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(ProcurementDetail::class);
    }

    public function approvals()
    {
        return $this->hasMany(Approval::class);
    }

    public function vendorQuotes()
    {
        return $this->hasMany(VendorQuote::class);
    }

    public function logs()
    {
        return $this->hasMany(ProcurementLog::class);
    }

    public function isDraft()
    {
        return $this->status === 'draft';
    }

    public function isSubmitted()
    {
        return $this->status === 'diajukan';
    }

    public function isApproved()
    {
        return $this->status === 'disetujui';
    }

    public function isRejected()
    {
        return $this->status === 'ditolak';
    }

    public function isProcessed()
    {
        return $this->status === 'diproses';
    }

    public function isCompleted()
    {
        return $this->status === 'selesai';
    }
}