<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;

class UserMenu extends Component
{
    #[On('profile-updated')] 
    public function refresh()
    {
        // This method handles the event and triggers a re-render
    }

    public function render()
    {
        return view('livewire.user-menu');
    }
}
