<?php

namespace App\Console\Commands;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Console\Command;

class SendSuggestions extends Command
{
    protected $signature = 'notificaciones:sugerencias';
    protected $description = 'Envía sugerencias automáticas a alumnos, profesores y empresas';

    public function handle()
    {
        $this->info('Enviando sugerencias a usuarios...');

        $usuarios = User::all();

        foreach ($usuarios as $user) {
            match ($user->role) {
                'alumno' => $this->sugerenciasAlumno($user),
                'usuario' => $this->sugerenciasAlumno($user),
                'profesor' => $this->sugerenciasProfesor($user),
                'empresa' => $this->sugerenciasEmpresa($user),
                default => null,
            };
        }

        $this->info('Notificaciones de sugerencia enviadas correctamente.');
    }

    protected function sugerenciasAlumno($user)
    {
        if ($user->applications()->count() === 0) {
            $this->notificar($user, 'sugerencia', '¿Buscas oportunidades?', 'Hay nuevas ofertas disponibles esta semana. ¡Échales un vistazo y postúlate!');
        }

        if ($user->projects()->count() === 0) {
            $this->notificar($user, 'sugerencia', '¡Publica tu primer proyecto!', 'Sube un proyecto escolar para destacarte entre los perfiles más activos.');
        }

        if ($user->favorites()->count() === 0) {
            $this->notificar($user, 'sugerencia', '¿Te interesa alguna oferta?', 'Guarda tus ofertas favoritas para no perderlas de vista.');
        }
    }

    protected function sugerenciasProfesor($user)
    {
        if (method_exists($user, 'validatedProjects') && $user->validatedProjects()->count() === 0) {
            $this->notificar($user, 'sugerencia', 'Revisa proyectos pendientes', 'Hay proyectos escolares esperando tu validación.');
        }

        if (method_exists($user, 'publishedProjects') && $user->publishedProjects()->count() === 0) {
            $this->notificar($user, 'sugerencia', 'Comparte un proyecto destacado', 'Publica un proyecto para motivar al alumnado con buenos ejemplos.');
        }
    }

    protected function sugerenciasEmpresa($user)
    {
        if ($user->jobOffers()->count() === 0) {
            $this->notificar($user, 'sugerencia', '¿Tienes nuevas vacantes?', 'Publica una nueva oferta y descubre perfiles con talento.');
        }

        if ($user->jobOffers()->whereDoesntHave('applications')->count() > 0) {
            $this->notificar($user, 'sugerencia', 'Mejora tu oferta', 'Algunas de tus ofertas no han recibido postulaciones. Mejora su título o descripción.');
        }

        if (empty($user->description) || empty($user->logo)) {
            $this->notificar($user, 'sugerencia', 'Completa tu perfil', 'Las ofertas con perfiles completos generan más confianza en los candidatos.');
        }
    }

    protected function notificar($user, $type, $title, $message)
    {
        $existe = Notification::where('user_id', $user->id)
            ->where('type', $type)
            ->where('title', $title)
            ->exists();

        if (!$existe) {
            Notification::create([
                'user_id' => $user->id,
                'type' => $type,
                'title' => $title,
                'message' => $message,
            ]);
        }
    }
}
