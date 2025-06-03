<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Task $task)
    {
        // Les managers peuvent voir toutes les tâches de leur équipe
        if ($user->role === 'manager' && $user->belongsToTeam($task->team)) {
            return true;
        }

        // Les collaborateurs ne peuvent voir que leurs propres tâches
        return $user->id === $task->assigned_to && $user->belongsToTeam($task->team);
    }

    public function create(User $user)
    {
        // Seuls les managers peuvent créer des tâches
        return $user->role === 'manager';
    }

    public function update(User $user, Task $task)
    {
        // Seuls les managers peuvent modifier des tâches
        return $user->role === 'manager' && $user->belongsToTeam($task->team);
    }

    public function delete(User $user, Task $task)
    {
        // Seuls les managers peuvent supprimer des tâches
        return $user->role === 'manager' && $user->belongsToTeam($task->team);
    }

    public function start(User $user, Task $task)
    {
        // Seul le collaborateur assigné à la tâche peut la démarrer
        return $user->id === $task->assigned_to && $user->belongsToTeam($task->team);
    }

    public function complete(User $user, Task $task)
    {
        // Seul le collaborateur assigné à la tâche peut la marquer comme terminée
        return $user->id === $task->assigned_to && $user->belongsToTeam($task->team);
    }
}
