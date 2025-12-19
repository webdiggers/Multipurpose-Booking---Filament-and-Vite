<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'phone',
        'phone_verified_at',
        'whatsapp_verification_code',
        'verification_expires_at',
        'role',
        'email',
        'password',
        'address',
        'city',
        'state',
        'country',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'whatsapp_verification_code',
    ];

    protected $casts = [
        'phone_verified_at' => 'datetime',
        'verification_expires_at' => 'datetime',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isReception(): bool
    {
        return $this->role === 'reception';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    public function canAccessPanel(Panel $panel): bool
    {
        // Log for debugging
        \Log::info("User {$this->email} (role: {$this->role}) trying to access panel: {$panel->getId()}");
        
        // Allow admin and reception roles
        $canAccess = $this->isAdmin() || $this->isReception();
        
        \Log::info("Access granted: " . ($canAccess ? 'Yes' : 'No'));
        
        return $canAccess;
    }

    public function sendWhatsAppVerification(): string
    {
        // Generate 6-digit code
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        $this->whatsapp_verification_code = $code;
        $this->verification_expires_at = now()->addMinutes(10);
        $this->save();

        // TODO: Integrate with Twilio WhatsApp API
        // For now, just return the code (for development)
        \Log::info("Verification code for {$this->phone}: {$code}");

        return $code;
    }

    public function verifyCode(string $code): bool
    {
        if ($this->whatsapp_verification_code === $code && 
            $this->verification_expires_at && 
            $this->verification_expires_at->isFuture()) {
            
            $this->phone_verified_at = now();
            $this->whatsapp_verification_code = null;
            $this->verification_expires_at = null;
            $this->save();

            return true;
        }

        return false;
    }

    public function hasVerifiedPhone(): bool
    {
        return !is_null($this->phone_verified_at);
    }
}
