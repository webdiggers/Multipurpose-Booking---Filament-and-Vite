<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class Register extends Component
{
    public $registrationMethod;
    public $shownFields = [];
    public $requiredFields = [];

    // Form Fields
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    
    // Dynamic Fields
    public $first_name = '';
    public $last_name = '';
    public $phone = '';
    public $address = '';
    public $city = '';
    public $state = '';
    public $country = '';

    public function mount()
    {
        $this->registrationMethod = Setting::get('registration_method', 'email');

        if ($this->registrationMethod === 'phone') {
            return redirect()->route('login');
        }

        if ($this->registrationMethod === 'full') {
            $this->shownFields = Setting::get('registration_fields_shown', []);
            $this->requiredFields = Setting::get('registration_fields_required', []);
        } else {
            // Email only mode
            $this->shownFields = [];
            $this->requiredFields = [];
        }
    }

    public function register()
    {
        $rules = [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ];

        if ($this->registrationMethod === 'full') {
            foreach ($this->shownFields as $field) {
                if ($field === 'password' || $field === 'email') continue; // Handled above

                if (in_array($field, $this->requiredFields)) {
                    $rules[$field] = 'required';
                    if ($field === 'phone') $rules[$field] .= '|digits:10|unique:users,phone';
                } else {
                    $rules[$field] = 'nullable';
                    if ($field === 'phone') $rules[$field] .= '|digits:10|unique:users,phone';
                }
            }
        }

        $this->validate($rules);

        $userData = [
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role' => 'customer',
        ];

        // Add dynamic fields
        if ($this->registrationMethod === 'full') {
            foreach ($this->shownFields as $field) {
                if ($field === 'password' || $field === 'email') continue;
                $userData[$field] = $this->$field;
            }
            
            // Construct name from first_name and last_name if available
            if (isset($userData['first_name']) || isset($userData['last_name'])) {
                $userData['name'] = trim(($userData['first_name'] ?? '') . ' ' . ($userData['last_name'] ?? ''));
            } else {
                $userData['name'] = explode('@', $this->email)[0];
            }
        } else {
            $userData['name'] = explode('@', $this->email)[0];
        }

        $user = User::create($userData);

        Auth::login($user);

        return redirect()->intended(route('studios.index'));
    }

    public function render()
    {
        return view('livewire.auth.register')
            ->layout('components.layouts.app');
    }
}
