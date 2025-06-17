<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserSession;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class UserPresenceService
{
    // Constantes pour les limites légales
    const DAILY_LIMIT_HOURS = 10;
    const WEEKLY_LIMIT_HOURS = 44;
    const DAILY_LIMIT_MINUTES = 600;
    const WEEKLY_LIMIT_MINUTES = 2640;

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
     * Plafonne à 10h par jour maximum
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

            // IMPORTANT: Plafonner à 10h par jour (600 minutes)
            $dailyPresence[$date]['minutes'] = min($dailyPresence[$date]['minutes'], self::DAILY_LIMIT_MINUTES);
        }

        // Convertir en tableau indexé
        return array_values($dailyPresence);
    }

    /**
     * Récupère un résumé des présences pour tous les collaborateurs d'une équipe
     * Applique un plafonnement à 10h/jour et 44h/semaine
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
            // Récupérer toutes les sessions avec plafonnement par jour
            $sessions = UserSession::where('user_id', $collaborateur->id)
                ->where('team_id', $team->id)
                ->where('login_time', '>=', $startDate)
                ->where('login_time', '<=', $endDate)
                ->orderBy('login_time')
                ->get();

            // Calculer les heures par jour avec plafonnement
            $dailyHours = [];
            foreach ($sessions as $session) {
                $date = $session->login_time->format('Y-m-d');
                $duration = $session->getDurationInMinutesAttribute();

                if (!isset($dailyHours[$date])) {
                    $dailyHours[$date] = 0;
                }

                $dailyHours[$date] += $duration;

                // Plafonner à 10h par jour (600 minutes)
                $dailyHours[$date] = min($dailyHours[$date], self::DAILY_LIMIT_MINUTES);
            }

            // Calculer le total des minutes avec plafonnement quotidien
            $totalMinutes = array_sum($dailyHours);

            // Plafonner le total hebdomadaire à 44h (2640 minutes)
            $totalMinutes = min($totalMinutes, self::WEEKLY_LIMIT_MINUTES);

            // Calculer la moyenne quotidienne réelle (seulement sur les jours travaillés)
            $daysWorked = count($dailyHours);
            $averageDailyHours = $daysWorked > 0 ? round($totalMinutes / (60 * $daysWorked), 1) : 0;

            // Plafonner la moyenne à 10h/jour
            $averageDailyHours = min($averageDailyHours, self::DAILY_LIMIT_HOURS);

            $totalSessions = UserSession::where('user_id', $collaborateur->id)
                ->where('team_id', $team->id)
                ->where('login_time', '>=', $startDate)
                ->where('login_time', '<=', $endDate)
                ->count();

            $summary[] = [
                'id' => $collaborateur->id,
                'name' => $collaborateur->name,
                'totalHours' => min(round($totalMinutes / 60, 1), self::WEEKLY_LIMIT_HOURS), // Plafonné à 44h
                'averageDailyHours' => $averageDailyHours,
                'sessions' => $totalSessions,
                'daysWorked' => $daysWorked // Information utile pour debug
            ];
        }

        // Trier par heures totales (du plus au moins)
        usort($summary, function ($a, $b) {
            return $b['totalHours'] <=> $a['totalHours'];
        });

        return $summary;
    }

    /**
     * Debug: Affiche les détails de calcul pour un utilisateur
     */
    public function debugUserPresence(User $user, Team $team, $days = 7)
    {
        $startDate = Carbon::now()->subDays($days);
        $endDate = Carbon::now();

        $sessions = UserSession::where('user_id', $user->id)
            ->where('team_id', $team->id)
            ->where('login_time', '>=', $startDate)
            ->where('login_time', '<=', $endDate)
            ->orderBy('login_time')
            ->get();

        $debug = [
            'user' => $user->name,
            'period' => $startDate->format('Y-m-d') . ' to ' . $endDate->format('Y-m-d'),
            'total_sessions' => $sessions->count(),
            'daily_breakdown' => []
        ];

        $dailyHours = [];
        foreach ($sessions as $session) {
            $date = $session->login_time->format('Y-m-d');
            $duration = $session->getDurationInMinutesAttribute();
            $durationHours = round($duration / 60, 2);

            if (!isset($dailyHours[$date])) {
                $dailyHours[$date] = 0;
                $debug['daily_breakdown'][$date] = [
                    'sessions' => 0,
                    'total_minutes' => 0,
                    'total_hours' => 0,
                    'capped_hours' => 0
                ];
            }

            $dailyHours[$date] += $duration;
            $debug['daily_breakdown'][$date]['sessions']++;
            $debug['daily_breakdown'][$date]['total_minutes'] = $dailyHours[$date];
            $debug['daily_breakdown'][$date]['total_hours'] = round($dailyHours[$date] / 60, 2);
            $debug['daily_breakdown'][$date]['capped_hours'] = min(round($dailyHours[$date] / 60, 2), self::DAILY_LIMIT_HOURS);
        }

        return $debug;
    }
}
