<?php

namespace App\Http\Middleware;

use App\Models\UserSession;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class TrackUserSession
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Vérifie si l'utilisateur vient de se connecter
            if (!Session::has('login_tracked')) {
                $currentTeam = $user->currentTeam;

                // Enregistre le login
                UserSession::create([
                    'user_id' => $user->id,
                    'team_id' => $currentTeam ? $currentTeam->id : null,
                    'login_time' => now(),
                ]);

                // Marque que le login a été enregistré pour cette session
                Session::put('login_tracked', true);
                Session::put('current_session_id', UserSession::where('user_id', $user->id)
                    ->whereNull('logout_time')
                    ->latest('login_time')
                    ->first()->id);
            }
        }

        return $next($request);
    }
}
