<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use PragmaRX\Google2FA\Google2FA;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'photo',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
        'two_factor_enabled'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes'
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
            'two_factor_enabled' => 'boolean'
        ];
    }

    public function getPhotoUrlAttribute()
    {
        if ($this->photo) {
            return asset('storage/photos/' . $this->photo);
        }
        return 'https://ui-avatars.com/api/?background=0D8ABC&color=fff&name=' . urlencode($this->name);
    }

    // ==================== 2FA METHODS ====================

    public function hasTwoFactorEnabled(): bool
    {
        return $this->two_factor_enabled && $this->two_factor_secret !== null;
    }

    public function generateTwoFactorSecret(): string
    {
        $google2fa = new Google2FA();
        $this->two_factor_secret = $google2fa->generateSecretKey();
        $this->two_factor_recovery_codes = json_encode($this->generateRecoveryCodes());
        $this->save();
        
        return $this->two_factor_secret;
    }

    public function confirmTwoFactor(string $code): bool
    {
        $google2fa = new Google2FA();
        
        if ($google2fa->verifyKey($this->two_factor_secret, $code)) {
            $this->two_factor_confirmed_at = now();
            $this->two_factor_enabled = true;
            $this->save();
            return true;
        }
        
        return false;
    }

    public function verifyTwoFactor(string $code): bool
    {
        if (!$this->hasTwoFactorEnabled()) {
            return false;
        }
        
        $google2fa = new Google2FA();
        return $google2fa->verifyKey($this->two_factor_secret, $code);
    }

    public function getTwoFactorQrCodeUrl(): string
    {
        $google2fa = new Google2FA();
        return $google2fa->getQRCodeUrl(
            'PERUMDAM Tirta Bengkayang',
            $this->email,
            $this->two_factor_secret
        );
    }

    public function generateRecoveryCodes(): array
    {
        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $codes[] = strtoupper(substr(md5(uniqid()), 0, 10));
        }
        return $codes;
    }

    public function getRecoveryCodes(): array
    {
        return json_decode($this->two_factor_recovery_codes, true) ?? [];
    }

    public function regenerateRecoveryCodes(): void
    {
        $this->two_factor_recovery_codes = json_encode($this->generateRecoveryCodes());
        $this->save();
    }

    public function disableTwoFactor(): void
    {
        $this->two_factor_secret = null;
        $this->two_factor_recovery_codes = null;
        $this->two_factor_confirmed_at = null;
        $this->two_factor_enabled = false;
        $this->save();
    }
}