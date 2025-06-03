<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TeamAccessMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Vérifie si la requête concerne une route d'équipe
        if ($request->is('teams*') || $request->is('admin/teams*') || $request->is('projects*')) {
            // Vérifie si l'utilisateur est connecté et s'il est manager ou administrateur
            if (!$request->user() || !in_array($request->user()->role, ['manager', 'administrateur'])) {
                return redirect()->route('access.denied')->with('error', 'Accès non autorisé aux fonctionnalités d\'équipe');
            }
        }

        return $next($request);
    }
}
