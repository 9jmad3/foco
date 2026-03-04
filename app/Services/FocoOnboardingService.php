<?php

namespace App\Services;

use App\Models\BlockType;
use App\Models\Template;
use App\Models\TemplateBlock;
use App\Models\UserSetting;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class FocoOnboardingService
{
    /**
     * Crea defaults mínimos para que el usuario pueda usar FOCO en 30 segundos:
     * - Block types base
     * - Plantilla "Día normal" con 3 bloques
     * - User settings con plantilla por defecto
     *
     * Es idempotente: si el usuario ya tiene plantillas o tipos, no hace nada.
     */
    public function onboard(User $user): void
    {
        // Si el usuario ya tiene algo creado, no tocamos nada.
        if (Template::where('user_id', $user->id)->exists() || BlockType::where('user_id', $user->id)->exists()) {
            UserSetting::firstOrCreate(
                ['user_id' => $user->id],
                ['max_daily_blocks' => 3, 'strict_mode' => true]
            );
            return;
        }

        DB::transaction(function () use ($user) {
            $settings = UserSetting::firstOrCreate(
                ['user_id' => $user->id],
                ['max_daily_blocks' => 3, 'strict_mode' => true]
            );

            // Tipos base (colores sobrios, dentro del estilo FOCO)
            $types = [
                ['name' => 'Trabajo',  'color' => '#234F3F', 'sort_order' => 10],
                ['name' => 'Gym',      'color' => '#234F3F', 'sort_order' => 20],
                ['name' => 'Baile',    'color' => '#234F3F', 'sort_order' => 30],
                ['name' => 'Proyecto', 'color' => '#234F3F', 'sort_order' => 40],
                ['name' => 'Personal', 'color' => '#234F3F', 'sort_order' => 50],
            ];

            $typeMap = [];
            foreach ($types as $t) {
                $bt = BlockType::create([
                    'user_id' => $user->id,
                    'name' => $t['name'],
                    'color' => $t['color'],
                    'sort_order' => $t['sort_order'],
                ]);
                $typeMap[$t['name']] = $bt->id;
            }

            // Plantilla base
            Template::where('user_id', $user->id)->update(['is_default' => false]);

            $tpl = Template::create([
                'user_id' => $user->id,
                'name' => 'Día normal',
                'is_default' => true,
            ]);

            $blocks = [
                ['type' => 'Trabajo',  'title' => 'Trabajo profundo',     'mins' => 90, 'sort' => 10],
                ['type' => 'Gym',      'title' => 'Entrenamiento fuerza', 'mins' => 60, 'sort' => 20],
                ['type' => 'Proyecto', 'title' => 'Proyecto personal',    'mins' => 60, 'sort' => 30],
            ];

            foreach ($blocks as $b) {
                TemplateBlock::create([
                    'template_id' => $tpl->id,
                    'block_type_id' => $typeMap[$b['type']],
                    'title' => $b['title'],
                    'estimated_minutes' => $b['mins'],
                    'sort_order' => $b['sort'],
                ]);
            }

            $settings->update(['default_template_id' => $tpl->id]);
        });
    }
}
