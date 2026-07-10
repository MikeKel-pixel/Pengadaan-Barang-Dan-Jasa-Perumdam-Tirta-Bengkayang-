<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'photo',
        'two_factor_enabled',
        'two_factor_code',
        'two_factor_expires_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_code',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'two_factor_enabled' => 'boolean',
        'two_factor_expires_at' => 'datetime'
    ];

    // ==================== ACCESSOR FOTO ====================
    public function getPhotoUrlAttribute()
    {
        if ($this->photo) {
            return asset('storage/photos/' . $this->photo);
        }
        return 'https://ui-avatars.com/api/?background=0D8ABC&color=fff&name=' . urlencode($this->name);
    }

    // ==================== METHOD 2FA ====================

    /**
     * Cek apakah 2FA aktif
     */
    public function hasTwoFactorEnabled(): bool
    {
        return $this->two_factor_enabled === true;
    }

    /**
     * Generate kode 6 digit random
     */
    public function generateTwoFactorCode(): string
    {
        $code = sprintf("%06d", rand(100000, 999999));
        $this->two_factor_code = $code;
        $this->two_factor_expires_at = now()->addMinutes(10);
        $this->save();
        return $code;
    }

    /**
     * Kirim kode 2FA ke email (DENGAN ERROR HANDLING)
     */
    public function sendTwoFactorCode()
    {
        $code = $this->generateTwoFactorCode();

        try {
            Mail::send('emails.two-factor-code', ['code' => $code, 'name' => $this->name], function ($message) {
                $message->to($this->email, $this->name)
                        ->subject('Kode Verifikasi Two Factor Authentication - PERUMDAM');
            });
            
            // Log sukses
            Log::info('Email 2FA terkirim ke: ' . $this->email);
            
        } catch (\Exception $e) {
            // Log error (tidak mengganggu proses login)
            Log::error('Gagal kirim email 2FA: ' . $e->getMessage());
            
            // Opsi: lanjutkan proses tanpa email (untuk development)
            // Atau throw exception jika ingin menghentikan proses
            // throw $e;
        }
    }

    /**
     * Verifikasi kode 2FA
     */
    public function verifyTwoFactorCode(string $code): bool
    {
        if (!$this->two_factor_code) {
            return false;
        }

        // Cek apakah kode masih berlaku
        if ($this->two_factor_expires_at && now()->greaterThan($this->two_factor_expires_at)) {
            return false;
        }

        return $this->two_factor_code === $code;
    }

    /**
     * Hapus kode setelah digunakan
     */
    public function clearTwoFactorCode()
    {
        $this->two_factor_code = null;
        $this->two_factor_expires_at = null;
        $this->save();
    }

    /**
     * Aktifkan 2FA
     */
    public function enableTwoFactor()
    {
        $this->two_factor_enabled = true;
        $this->save();
    }

    /**
     * Nonaktifkan 2FA
     */
    public function disableTwoFactor()
    {
        $this->two_factor_enabled = false;
        $this->two_factor_code = null;
        $this->two_factor_expires_at = null;
        $this->save();
    }
}