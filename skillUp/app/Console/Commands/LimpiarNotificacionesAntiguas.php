<?php

namespace App\Console\Commands;

use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class LimpiarNotificacionesAntiguas extends Command
{
    protected $signature = 'notificaciones:limpiar';
    protected $description = 'Elimina notificaciones antiguas para todos los usuarios';

    public function handle()
    {
        $limite = Carbon::now()->subDays(3);

        $eliminadas = Notification::where('created_at', '<', $limite)->delete();

        $this->info("Se eliminaron {$eliminadas} notificaciones antiguas.");
    }
}
