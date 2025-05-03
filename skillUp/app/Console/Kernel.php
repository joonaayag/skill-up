<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        EnviarSugerenciasUsuarios::class,
        LimpiarNotificacionesAntiguas::class,

    ];

    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('notificaciones:limpiar')->daily();  
        $schedule->command('notificaciones:sugerencias')->daily();
    }

    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
