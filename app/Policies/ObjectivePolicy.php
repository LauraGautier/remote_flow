<?php

namespace App\Policies;

use App\Models\Objective;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ObjectivePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Objective $objective)
    {
        // Les utilisateurs peuvent voir les objectifs des projets auxquels ils ont accès
        return $user->belongsToTeam($objective->project->team);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Objective $objective)
    {
        // Seuls les managers peuvent modifier des objectifs
        return $user->role === 'manager' && $user->belongsToTeam($objective->project->team);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Objective $objective)
    {
        // Seuls les managers peuvent supprimer des objectifs
        return $user->role === 'manager' && $user->belongsToTeam($objective->project->team);
    }
}
