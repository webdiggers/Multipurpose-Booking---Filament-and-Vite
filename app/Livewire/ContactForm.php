<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ContactMessage;

class ContactForm extends Component
{
    public $first_name;
    public $last_name;
    public $email;
    public $phone;
    public $message;

    protected $rules = [
        'first_name' => 'required|min:2',
        'last_name' => 'required|min:2',
        'email' => 'required|email',
        'phone' => 'nullable',
        'message' => 'required|min:10',
    ];

    public function submit()
    {
        $this->validate();

        ContactMessage::create([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'message' => $this->message,
        ]);

        session()->flash('success', 'Thank you for your message. We will get back to you soon!');

        $this->reset();
    }

    public function render()
    {
        return view('livewire.contact-form');
    }
}
