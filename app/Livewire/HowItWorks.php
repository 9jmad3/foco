<?php

namespace App\Livewire;

use Livewire\Component;

class HowItWorks extends Component
{
    public function render()
    {
        return view('livewire.how-it-works')
            ->layout('layouts.guest');
    }
}
