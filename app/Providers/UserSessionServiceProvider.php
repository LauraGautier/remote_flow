<?php

namespace App\Providers;

use App\Models\UserSession;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;

class UserSessionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Écouter l'événement de déconnexion
        Event::listen(Logout::class, function (Logout $event) {
            if (Session::has('current_session_id')) {
                $sessionId = Session::get('current_session_id');
                $session = UserSession::find($sessionId);

                if ($session) {
                    $session->update([
                        'logout_time' => now()
                    ]);
                }
            }
        });

        // On peut également écouter l'événement de connexion ici
        Event::listen(Login::class, function (Login $event) {
            $user = $event->user;
            $currentTeam = $user->currentTeam;

            // Enregistrer le login
            $session = UserSession::create([
                'user_id' => $user->id,
                'team_id' => $currentTeam ? $currentTeam->id : null,
                'login_time' => now(),
            ]);

            // Stocker l'ID de session pour pouvoir l'utiliser lors de la déconnexion
            Session::put('current_session_id', $session->id);
        });
    }
}
