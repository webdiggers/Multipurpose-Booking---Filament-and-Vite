<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserProfile extends Component
{
    public $name;
    public $email;
    public $phone;
    public $address;
    public $city;
    public $state;
    public $country;
    public $password;
    public $password_confirmation;
    public $registrationType;

    public function mount()
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone;
        $this->address = $user->address;
        $this->city = $user->city;
        $this->state = $user->state;
        $this->country = $user->country;
        $this->registrationType = trim(\App\Models\Setting::get('registration_type', 'phone'));
    }

    public function updateProfile()
    {
        $user = Auth::user();

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        ];

        if ($this->registrationType === 'full') {
            $rules['phone'] = ['required', 'string', 'max:20', Rule::unique('users')->ignore($user->id)];
            $rules['address'] = ['nullable', 'string', 'max:255'];
            $rules['city'] = ['nullable', 'string', 'max:255'];
            $rules['state'] = ['nullable', 'string', 'max:255'];
            $rules['country'] = ['nullable', 'string', 'max:255'];
        }

        if ($this->registrationType === 'email' || $this->registrationType === 'full') {
            $rules['password'] = ['nullable', 'string', 'min:8', 'confirmed'];
        }

        $this->validate($rules);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
        ];

        if ($this->registrationType === 'full') {
            $data['phone'] = $this->phone;
            $data['address'] = $this->address;
            $data['city'] = $this->city;
            $data['state'] = $this->state;
            $data['country'] = $this->country;
        }

        if (($this->registrationType === 'email' || $this->registrationType === 'full') && !empty($this->password)) {
            $data['password'] = \Illuminate\Support\Facades\Hash::make($this->password);
        }

        $user->update($data);

        $this->dispatch('profile-updated');
        
        // Clear password fields after update
        $this->password = '';
        $this->password_confirmation = '';

        session()->flash('success', 'Profile updated successfully.');
    }

    public function render()
    {
        return view('livewire.user-profile');
    }
}
