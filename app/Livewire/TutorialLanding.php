<?php

namespace App\Livewire;

use Livewire\Component;

class TutorialLanding extends Component
{
    public function render()
    {
        return view('livewire.tutorial-landing')
            ->layout('layouts.app');
    }
}
