<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectBasedOnRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Si l'utilisateur est authentifié et essaie d'accéder à la route dashboard
        if ($request->user() && $request->routeIs('dashboard')) {
            // Rediriger selon le rôle
            switch ($request->user()->role) {
                case 'administrateur':
                    return redirect()->route('admin.dashboard');
                case 'manager':
                    return redirect()->route('manager.dashboard');
                case 'collaborateur':
                    return redirect()->route('collaborateur.dashboard');
                default:
                    // Garder le comportement par défaut pour les autres rôles
                    return $next($request);
            }
        }

        return $next($request);
    }
}
