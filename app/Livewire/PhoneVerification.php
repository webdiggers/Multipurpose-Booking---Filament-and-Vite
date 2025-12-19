<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;

class PhoneVerification extends Component
{
    public $phone = '';
    public $verificationCode = '';
    public $isCodeSent = false;

    protected $rules = [
        'phone' => 'required|digits:10',
    ];

    public function sendCode()
    {
        $this->validate(['phone' => 'required|digits:10']);
        
        // Generate 4-digit OTP
        $code = rand(1000, 9999);
        
        // For development: Store in session (can be upgraded to Twilio/SMS later)
        session([
            'verification_code' => $code,
            'verification_phone' => $this->phone,
            'code_expires_at' => now()->addMinutes(10)
        ]);
        
        // In development, log the code (remove in production)
        \Log::info("OTP for {$this->phone}: {$code}");
        
        $this->isCodeSent = true;
        session()->flash('message', "Verification code sent! (Check logs for code: {$code})");
    }

    public function verifyCode()
    {
        $this->validate(['verificationCode' => 'required|digits:4']);

        $sessionCode = session('verification_code');
        $sessionPhone = session('verification_phone');
        $expiresAt = session('code_expires_at');

        // Check if code is valid and not expired
        if (!$sessionCode || !$sessionPhone) {
            $this->addError('verificationCode', 'Please request a new verification code.');
            return;
        }

        if (now()->greaterThan($expiresAt)) {
            $this->addError('verificationCode', 'Verification code has expired.');
            session()->forget(['verification_code', 'verification_phone', 'code_expires_at']);
            $this->isCodeSent = false;
            return;
        }

        if ($this->verificationCode != $sessionCode) {
            $this->addError('verificationCode', 'Invalid verification code.');
            return;
        }

        // Create or get user
        $user = User::firstOrCreate(
            ['phone' => $sessionPhone],
            [
                'email' => $sessionPhone . '@customer.local', // Generate email from phone
                'password' => bcrypt(str()->random(16)), // Random password
                'phone_verified_at' => now(),
                'role' => 'customer'
            ]
        );

        // Update phone_verified_at if not set
        if (!$user->phone_verified_at) {
            $user->update(['phone_verified_at' => now()]);
        }

        // Clear verification session data
        session()->forget(['verification_code', 'verification_phone', 'code_expires_at']);

        // Login user
        auth()->login($user);

        // Redirect to intended url or studios index
        return redirect()->intended(route('studios.index'));
    }

    public function resendCode()
    {
        $this->isCodeSent = false;
        $this->verificationCode = '';
        $this->sendCode();
    }

    public function render()
    {
        return view('livewire.phone-verification')
            ->layout('components.layouts.app');
    }
}
