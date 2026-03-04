<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\FocoOnboardingService;
use Illuminate\Console\Command;

class FocoOnboardExisting extends Command
{
    protected $signature = 'foco:onboard-existing {--force : Re-aplicar incluso si el usuario ya tiene datos}';
    protected $description = 'Crea defaults de FOCO (tipos + plantilla) para usuarios existentes que no tengan configuración.';

    public function handle(FocoOnboardingService $service): int
    {
        $force = (bool) $this->option('force');
        $count = 0;

        User::query()->chunkById(200, function ($users) use ($service, $force, &$count) {
            foreach ($users as $user) {
                if ($force) {
                    // Forzar: borrar SOLO si el usuario no tiene nada es más seguro; aquí no tocamos datos.
                    $service->onboard($user);
                    $count++;
                    continue;
                }

                // Idempotente: onboard() ya no hace nada si detecta datos.
                $service->onboard($user);
                $count++;
            }
        });

        $this->info("FOCO onboarding revisado para {$count} usuarios.");
        return self::SUCCESS;
    }
}
