<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserSession;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class UserPresenceService
{
    /**
     * Récupère les durées de présence par jour pour un utilisateur
     */
    public function getDailyPresenceForUser(User $user, $startDate = null, $endDate = null)
    {
        $startDate = $startDate ?? Carbon::now()->subDays(30);
        $endDate = $endDate ?? Carbon::now();

        $sessions = UserSession::where('user_id', $user->id)
            ->where('login_time', '>=', $startDate)
            ->where('login_time', '<=', $endDate)
            ->orderBy('login_time')
            ->get();

        return $this->calculateDailyPresence($sessions);
    }

    /**
     * Récupère les durées de présence par jour pour tous les membres d'une équipe
     */
    public function getDailyPresenceForTeam(Team $team, $startDate = null, $endDate = null)
    {
        $startDate = $startDate ?? Carbon::now()->subDays(30);
        $endDate = $endDate ?? Carbon::now();

        $userIds = $team->users->pluck('id')->toArray();

        $result = [];

        foreach ($userIds as $userId) {
            $user = User::find($userId);
            if ($user && $user->role === 'collaborateur') {
                $sessions = UserSession::where('user_id', $userId)
                    ->where('team_id', $team->id)
                    ->where('login_time', '>=', $startDate)
                    ->where('login_time', '<=', $endDate)
                    ->orderBy('login_time')
                    ->get();

                $dailyPresence = $this->calculateDailyPresence($sessions);

                $result[$userId] = [
                    'user' => $user->only('id', 'name', 'email'),
                    'presence' => $dailyPresence
                ];
            }
        }

        return $result;
    }

    /**
     * Calcule les durées de présence par jour à partir d'une collection de sessions
     */
    private function calculateDailyPresence(Collection $sessions)
    {
        $dailyPresence = [];

        foreach ($sessions as $session) {
            // Obtenir la date (uniquement le jour) pour regrouper les sessions
            $date = $session->login_time->format('Y-m-d');

            // Calculer la durée de cette session en minutes
            $duration = $session->getDurationInMinutesAttribute();

            // Ajouter ou mettre à jour la durée totale pour ce jour
            if (!isset($dailyPresence[$date])) {
                $dailyPresence[$date] = [
                    'date' => $date,
                    'minutes' => $duration,
                    'sessions' => 1
                ];
            } else {
                $dailyPresence[$date]['minutes'] += $duration;
                $dailyPresence[$date]['sessions']++;
            }
        }

        // Convertir en tableau indexé
        return array_values($dailyPresence);
    }

    /**
     * Récupère un résumé des présences pour tous les collaborateurs d'une équipe
     */
    public function getTeamPresenceSummary(Team $team, $days = 7)
    {
        $startDate = Carbon::now()->subDays($days);
        $endDate = Carbon::now();

        $collaborateurs = $team->users->filter(function ($user) {
            return $user->role === 'collaborateur';
        });

        $summary = [];

        foreach ($collaborateurs as $collaborateur) {
            $totalMinutes = UserSession::where('user_id', $collaborateur->id)
                ->where('team_id', $team->id)
                ->where('login_time', '>=', $startDate)
                ->where('login_time', '<=', $endDate)
                ->get()
                ->sum(function ($session) {
                    return $session->getDurationInMinutesAttribute();
                });

            $totalSessions = UserSession::where('user_id', $collaborateur->id)
                ->where('team_id', $team->id)
                ->where('login_time', '>=', $startDate)
                ->where('login_time', '<=', $endDate)
                ->count();

            $averageDailyHours = 0;
            if ($days > 0) {
                $averageDailyHours = round($totalMinutes / (60 * $days), 1);
            }

            $summary[] = [
                'id' => $collaborateur->id,
                'name' => $collaborateur->name,
                'totalHours' => round($totalMinutes / 60, 1),
                'averageDailyHours' => $averageDailyHours,
                'sessions' => $totalSessions
            ];
        }

        // Trier par heures totales (du plus au moins)
        usort($summary, function ($a, $b) {
            return $b['totalHours'] <=> $a['totalHours'];
        });

        return $summary;
    }
}
