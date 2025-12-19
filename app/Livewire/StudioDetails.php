<?php

namespace App\Livewire;

use App\Models\Studio;
use Livewire\Component;

class StudioDetails extends Component
{
    public Studio $studio;

    public function mount(Studio $studio)
    {
        $this->studio = $studio;
    }

    public function render()
    {
        return view('livewire.studio-details')
            ->layout('components.layouts.app');
    }
}
