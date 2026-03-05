<?php

namespace App\Livewire;

use Livewire\Component;

class NextSteps extends Component
{
    public function render()
    {
        return view('livewire.next-steps')
            ->layout('layouts.guest');
    }
}
