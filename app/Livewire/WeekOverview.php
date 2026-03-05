<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class WeekOverview extends Component
{
    public int $weekOffset = 0; // la vista es "tipo", weekOffset puede quedarse para futuro

    public function goToDay(int $weekday)
    {
        // ✅ Ajusta esto a cómo editas un día en tu semana.
        // Opción A: tu ruta de semana recibe weekday por querystring
        return redirect()->route('foco.week', ['day' => $weekday]);

        // Opción B:
        // return redirect()->to(route('foco.week') . '?weekday=' . $weekday);
    }

    private function weekDays(): array
    {
        // ISO: lunes=1 ... domingo=7
        return [
            ['weekday' => 1, 'label' => 'Lun'],
            ['weekday' => 2, 'label' => 'Mar'],
            ['weekday' => 3, 'label' => 'Mié'],
            ['weekday' => 4, 'label' => 'Jue'],
            ['weekday' => 5, 'label' => 'Vie'],
            ['weekday' => 6, 'label' => 'Sáb'],
            ['weekday' => 7, 'label' => 'Dom'],
        ];
    }

    public function render()
    {
        $userId = Auth::id();
        $days = $this->weekDays();
        $weekdays = array_column($days, 'weekday');

        $rows = DB::table('weekday_blocks as wb')
            ->join('library_blocks as lb', 'lb.id', '=', 'wb.library_block_id')
            ->leftJoin('block_types as bt', 'bt.id', '=', 'lb.block_type_id')
            ->where('wb.user_id', $userId)
            ->whereIn('wb.weekday', $weekdays)
            ->orderBy('wb.weekday')
            ->orderBy('wb.position') // ✅ orden principal del día
            ->orderByRaw("CASE WHEN wb.start_time IS NULL THEN 1 ELSE 0 END")
            ->orderBy('wb.start_time') // ✅ si hay hora, ordena
            ->orderBy('wb.id')
            ->get([
                'wb.weekday',
                'wb.position',
                'wb.start_time', // ✅
                'wb.priority',
                'lb.title',
                'lb.estimated_minutes',
                'bt.name as type_name',
            ]);

        // Agrupar por weekday
        $byWeekday = [];
        foreach ($weekdays as $w) $byWeekday[$w] = [];
        foreach ($rows as $r) $byWeekday[(int)$r->weekday][] = $r;

        return view('livewire.week-overview', [
            'days' => $days,
            'byWeekday' => $byWeekday,
        ])->layout('layouts.app');
    }
}
