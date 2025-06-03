<?php

namespace App\Models;

use Carbon\Carbon;

class ProductivityAlert
{
    // Nombre de jours au-delà duquel une tâche est considérée comme "en retard"
    public const ALERT_THRESHOLD_DAYS = 5;

    /**
     * Vérifie si un collaborateur a des tâches en cours depuis trop longtemps
     */
    public static function getCollaborateurDelayedTasks($userId)
    {
        $now = Carbon::now();
        $thresholdDate = $now->copy()->subDays(self::ALERT_THRESHOLD_DAYS);

        // Récupérer les tâches en cours depuis trop longtemps
        $delayedTasks = Task::where('assigned_to', $userId)
            ->where('status', 'in_progress')
            ->whereNotNull('start_time')
            ->where('start_time', '<', $thresholdDate)
            ->with(['team:id,name', 'project:id,name'])
            ->orderBy('start_time', 'asc')  // Les plus anciennes en premier
            ->get()
            ->map(function($task) use ($now) {
                $task->days_running = $now->diffInDays($task->start_time);
                return $task;
            });

        return $delayedTasks;
    }
}
