<?php

namespace App\Livewire;

use App\Models\Template;
use App\Models\UserSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class UserSettingsPage extends Component
{
    public int $maxDailyBlocks = 3;
    public bool $strictMode = true;
    public ?int $defaultTemplateId = null;

    public function mount(): void
    {
        $settings = UserSetting::firstOrCreate(
            ['user_id' => Auth::id()],
            ['max_daily_blocks' => 3, 'strict_mode' => true]
        );

        $this->maxDailyBlocks = (int) $settings->max_daily_blocks;
        $this->strictMode = (bool) $settings->strict_mode;
        $this->defaultTemplateId = $settings->default_template_id;
    }

    public function save(): void
    {
        $this->validate([
            'maxDailyBlocks' => 'required|integer|min:1|max:20',
            'strictMode' => 'required|boolean',
            'defaultTemplateId' => 'nullable|integer',
        ]);

        $userId = Auth::id();

        DB::transaction(function () use ($userId) {
            $settings = UserSetting::firstOrCreate(
                ['user_id' => $userId],
                ['max_daily_blocks' => 3, 'strict_mode' => true]
            );

            // Asegurar que la plantilla pertenece al usuario
            if ($this->defaultTemplateId) {
                $exists = Template::query()
                    ->where('user_id', $userId)
                    ->where('id', $this->defaultTemplateId)
                    ->exists();

                if (!$exists) {
                    $this->defaultTemplateId = null;
                }
            }

            $settings->update([
                'max_daily_blocks' => $this->maxDailyBlocks,
                'strict_mode' => $this->strictMode,
                'default_template_id' => $this->defaultTemplateId,
            ]);

            // Marcar is_default en templates (solo si hay default)
            if ($this->defaultTemplateId) {
                Template::where('user_id', $userId)->update(['is_default' => false]);
                Template::where('user_id', $userId)->where('id', $this->defaultTemplateId)->update(['is_default' => true]);
            }
        });

        $this->dispatch('toast', message: 'Ajustes guardados');
    }

    public function render()
    {
        $templates = Template::query()
            ->where('user_id', Auth::id())
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get(['id','name','is_default']);

        return view('livewire.user-settings-page', [
            'templates' => $templates,
        ])->layout('layouts.app');
    }
}
