<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

foreach (config('schedule') as $comando => $cfg) {
    $freq     = $cfg['frequencia'];
    $agendado = Schedule::command($comando);

    match ($freq) {
        'weeklyOn'       => $agendado->weeklyOn($cfg['dia'], $cfg['horario']),
        'dailyAt'        => $agendado->dailyAt($cfg['horario']),
        'hourlyAt'       => $agendado->hourlyAt($cfg['minuto']),
        'everyMinutes'   => $agendado->everyMinutes($cfg['minutos']),
        'everyHours'     => $agendado->everyHours($cfg['horas']),
        'everySixHours'  => $agendado->everySixHours(),
        'everyTwelveHours' => $agendado->everyTwelveHours(),
        default          => $agendado->$freq(),
    };
}
