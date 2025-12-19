<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class Login extends Component
{
    // Common
    public $loginMethod; // 'phone', 'email', 'full'

    // Phone Login
    public $phone = '';
    public $verificationCode = '';
    public $isCodeSent = false;

    // Email/Full Login
    public $email = '';
    public $password = '';

    protected function rules()
    {
        if ($this->loginMethod === 'phone') {
            return [
                'phone' => 'required|digits:10',
            ];
        }

        return [
            'email' => 'required|email',
            'password' => 'required',
        ];
    }

    public function mount()
    {
        $this->loginMethod = Setting::get('registration_method', 'email');
        
        // If method is 'full', we treat login same as 'email' (Email + Password)
        if ($this->loginMethod === 'full') {
            // $this->loginMethod = 'email'; // Keep it as 'full' to distinguish if needed, but logic is same
        }
    }

    // Phone Login Methods
    public function sendCode()
    {
        $this->validate(['phone' => 'required|digits:10']);
        
        // Generate 4-digit OTP
        $code = rand(1000, 9999);
        
        // For development: Store in session
        session([
            'verification_code' => $code,
            'verification_phone' => $this->phone,
            'code_expires_at' => now()->addMinutes(10)
        ]);
        
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
                'email' => $sessionPhone . '@customer.local',
                'password' => Hash::make(str()->random(16)),
                'phone_verified_at' => now(),
                'role' => 'customer',
                'name' => 'Guest ' . substr($sessionPhone, -4)
            ]
        );

        if (!$user->phone_verified_at) {
            $user->update(['phone_verified_at' => now()]);
        }

        session()->forget(['verification_code', 'verification_phone', 'code_expires_at']);

        Auth::login($user);

        return redirect()->intended(route('studios.index'));
    }

    public function resendCode()
    {
        $this->isCodeSent = false;
        $this->verificationCode = '';
        $this->sendCode();
    }

    // Email Login Method
    public function login()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
            session()->regenerate();
            return redirect()->intended(route('studios.index'));
        }

        $this->addError('email', 'The provided credentials do not match our records.');
    }

    public function render()
    {
        return view('livewire.auth.login')
            ->layout('components.layouts.app');
    }
}
