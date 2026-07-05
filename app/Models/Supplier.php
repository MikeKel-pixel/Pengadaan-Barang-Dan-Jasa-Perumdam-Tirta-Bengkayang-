<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_supplier',
        'alamat',
        'telepon',
        'email',
        'pic',
        'npwp',
        'bidang_usaha',
        'status',
        'registered_at',
        'verified_at',
        'verified_by',
        'rejection_reason'
    ];

    protected $casts = [
        'registered_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    // ========== VALIDASI MANUAL DI MODEL (AMAN) ==========
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // Verifikasi 1: Email harus unik
            if ($model->email) {
                $exists = static::where('email', $model->email)
                    ->where('id', '!=', $model->id)
                    ->exists();
                if ($exists) {
                    throw new \Exception('Email sudah terdaftar sebagai supplier lain.');
                }
            }
        });
    }

    public function vendorQuotes()
    {
        return $this->hasMany(VendorQuote::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function isVerified()
    {
        return $this->status === 'verified';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }
}