<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectPolicy
{
    use HandlesAuthorization;

    /**
     * Vérifier si l'utilisateur est un administrateur (pour court-circuiter les permissions)
     */
    public function before(User $user, $ability)
    {
        // Les administrateurs ont toutes les permissions
        if ($user->role === 'administrateur') {
            return true;
        }
    }

    public function viewAny(User $user)
    {
        // Tous les utilisateurs peuvent voir la liste des projets de leur équipe
        return true;
    }

    public function view(User $user, Project $project)
    {
        // Les utilisateurs peuvent voir les projets de leur équipe
        return $user->belongsToTeam($project->team);
    }

    public function create(User $user)
    {
        // Les managers et les administrateurs peuvent créer des projets
        return in_array($user->role, ['administrateur', 'manager']);
    }

    public function update(User $user, Project $project)
    {
        // Les managers et les administrateurs peuvent modifier des projets
        return ($user->role === 'manager' && $user->belongsToTeam($project->team))
               || $user->role === 'administrateur';
    }

    public function delete(User $user, Project $project)
    {
        // Les managers et les administrateurs peuvent supprimer des projets
        return ($user->role === 'manager' && $user->belongsToTeam($project->team))
               || $user->role === 'administrateur';
    }
}
