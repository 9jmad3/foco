<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\TodayFocus;
use App\Livewire\TemplatesManager;
use App\Livewire\UserSettingsPage;
use App\Livewire\WeeklySummary;
use App\Livewire\HowItWorks;
use App\Livewire\TemplateBlocksManager;
use App\Livewire\TemplateWeekAssigner;
use App\Livewire\LibraryBlocksManager;
use App\Livewire\WeekAssigner;
use App\Livewire\NextSteps;
use App\Livewire\TutorialLanding;
use App\Livewire\WeekOverview;

Route::get('/', function () {
    return redirect()->route('foco.today');
});

Route::get('/como-funciona', HowItWorks::class)
    ->name('foco.how');

Route::get('/next-steps', NextSteps::class)
    ->name('next.steps');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', fn () => redirect()->route('foco.today'));
    Route::get('/hoy', TodayFocus::class)->name('foco.today');
    Route::get('/plantillas', TemplatesManager::class)->name('foco.templates');
    Route::get('/ajustes', UserSettingsPage::class)->name('foco.settings');
    Route::get('/resumen', WeeklySummary::class)->name('foco.summary');
    Route::get('/plantillas/bloques', TemplateBlocksManager::class)->name('foco.blocks');
    Route::get('/plantillas/asignacion', TemplateWeekAssigner::class)->name('foco.assign');
    Route::get('/bloques', LibraryBlocksManager::class)->name('foco.library');
    Route::get('/semana', WeekAssigner::class)->name('foco.week');
    Route::get('/semana/vista', WeekOverview::class)->name('foco.week.overview');
    Route::get('/tutorial', TutorialLanding::class)->name('foco.tutorial');

});

