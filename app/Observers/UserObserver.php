<?php

namespace App\Observers;

use App\Models\User;
use App\Services\FocoOnboardingService;

class UserObserver
{
    public function created(User $user): void
    {
        // Onboarding automático: crea defaults solo si el usuario no tiene datos.
        app(FocoOnboardingService::class)->onboard($user);
    }
}
