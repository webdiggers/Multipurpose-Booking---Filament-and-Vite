<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Studio;

class StudioList extends Component
{
    public $search = '';
    public $sortBy = 'hourly_rate'; // hourly_rate, name, capacity
    public $sortDirection = 'asc';

    public function render()
    {
        $studios = Studio::query()
            ->where('is_active', true)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->get();

        return view('livewire.studio-list', [
            'studios' => $studios
        ])->layout('components.layouts.app');
    }

    public function setSortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }
}
