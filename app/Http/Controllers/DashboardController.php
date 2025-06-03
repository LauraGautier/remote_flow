<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Models\Objective;
use App\Models\User;
use App\Models\ProductivityAlert;
use App\Services\UserPresenceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function managerDashboard(Request $request)
    {
        $user = $request->user();

        // Vérifier que l'utilisateur est bien un manager
        if ($user->role !== 'manager') {
            return redirect()->route('access.denied')->with('error', 'Accès non autorisé');
        }

        $team = $user->currentTeam;

        // Bloc corrigé pour récupérer les données de présence
        $presenceService = new UserPresenceService();

        // Définir la période (7 derniers jours)
        $startDate = Carbon::now()->subDays(7);
        $endDate = Carbon::now();

        // Calculer le nombre de jours ouvrés dans cette période
        $workingDays = $this->countWorkingDays($startDate, $endDate);

        // Récupérer les données avec le nombre correct de jours ouvrés
        $presenceData = $presenceService->getTeamPresenceSummary($team, $workingDays);

        // Récupérer les données détaillées de présence par jour pour les 7 derniers jours
        $startDate = Carbon::now()->subDays(7);
        $endDate = Carbon::now();
        $dailyPresence = $presenceService->getDailyPresenceForTeam($team, $startDate, $endDate);

        // Projets récents avec leurs objectifs
        $recentProjects = Project::where('team_id', $team->id)
            ->with(['objectives'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($project) {
                $totalObjectives = $project->objectives->count();
                $completedObjectives = $project->objectives->where('is_completed', true)->count();

                return [
                    'id' => $project->id,
                    'name' => $project->name,
                    'status' => $project->status,
                    'created_at' => $project->created_at,
                    'objectivesStats' => [
                        'total' => $totalObjectives,
                        'completed' => $completedObjectives,
                        'percentage' => $totalObjectives > 0 ? round(($completedObjectives / $totalObjectives) * 100) : 0
                    ]
                ];
            });

        // Statistiques sur les projets
        $activeProjects = Project::where('team_id', $team->id)
            ->where('status', 'active')
            ->count();

        // Tâches en cours
        $inProgressTasks = Task::where('team_id', $team->id)
            ->where('status', 'in_progress')
            ->count();

        // Objectifs à atteindre (non complétés)
        $totalObjectives = Objective::whereIn('project_id', function($query) use ($team) {
            $query->select('id')
                  ->from('projects')
                  ->where('team_id', $team->id);
        })->count();

        $completedObjectives = Objective::whereIn('project_id', function($query) use ($team) {
            $query->select('id')
                  ->from('projects')
                  ->where('team_id', $team->id);
        })
        ->where('is_completed', true)
        ->count();

        $pendingObjectives = $totalObjectives - $completedObjectives;

        // Taux de réussite global (objectifs complétés / objectifs totaux)
        $completionRate = $totalObjectives > 0 ? round(($completedObjectives / $totalObjectives) * 100) : 0;

        // Performance des collaborateurs
        $collaborateurs = $team->users->filter(function ($user) {
            return $user->role === 'collaborateur';
        });

        $teamPerformance = [];

        foreach ($collaborateurs as $collaborateur) {
            $totalTasks = Task::where('team_id', $team->id)
                ->where('assigned_to', $collaborateur->id)
                ->count();

            $completedTasks = Task::where('team_id', $team->id)
                ->where('assigned_to', $collaborateur->id)
                ->where('status', 'completed')
                ->count();

            $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

            $teamPerformance[] = [
                'id' => $collaborateur->id,
                'name' => $collaborateur->name,
                'completedTasks' => $completedTasks,
                'totalTasks' => $totalTasks,
                'completionRate' => $completionRate
            ];
        }

        // Trier par taux de complétion (du plus haut au plus bas)
        usort($teamPerformance, function ($a, $b) {
            return $b['completionRate'] <=> $a['completionRate'];
        });

        // Limiter à 5 collaborateurs
        $teamPerformance = array_slice($teamPerformance, 0, 5);

        // Tâches urgentes
        $now = Carbon::now();
        $urgentTasks = Task::where('team_id', $team->id)
            ->where('status', '!=', 'completed')
            ->whereHas('project', function($query) {
                $query->where('status', 'active');
            })
            ->with(['assignedTo:id,name', 'project:id,name'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function ($task) use ($now) {
                // Considérer comme urgente si la tâche est en cours depuis plus de 7 jours
                $isUrgent = $task->start_time && $now->diffInDays(Carbon::parse($task->start_time)) >= 7;

                // Considérer comme très urgente si elle est en cours depuis plus de 14 jours
                $isVeryUrgent = $task->start_time && $now->diffInDays(Carbon::parse($task->start_time)) >= 14;

                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'assignedTo' => $task->assignedTo ? $task->assignedTo->name : 'Non assigné',
                    'project' => $task->project ? $task->project->name : 'N/A',
                    'dueDate' => $task->start_time ? 'En cours depuis ' . Carbon::parse($task->start_time)->diffForHumans() : 'Pas encore démarré',
                    'isUrgent' => $isUrgent,
                    'isVeryUrgent' => $isVeryUrgent
                ];
            })
            // Filtrer pour ne garder que les tâches urgentes ou très urgentes
            ->filter(function ($task) {
                return $task['isUrgent'] || $task['isVeryUrgent'];
            })
            ->values()
            ->all();

            // Activité récente
            $recentActivities = [];

            // Tâches récemment terminées
            $recentCompletedTasks = Task::where('team_id', $team->id)
            ->where('status', 'completed')
            ->with(['assignedTo:id,name', 'project:id,name'])
            ->whereDate('end_time', '>=', $now->subDay())
            ->orderBy('end_time', 'desc')
            ->take(5)
            ->get();

            foreach ($recentCompletedTasks as $task) {
            $recentActivities[] = [
                'type' => 'task_completed',
                'title' => 'Tâche terminée',
                'description' => $task->title . ' par ' . ($task->assignedTo ? $task->assignedTo->name : 'Inconnu'),
                'time' => $task->end_time
            ];
            }

            // Nouvelles tâches créées
            $recentCreatedTasks = Task::where('team_id', $team->id)
            ->with(['creator:id,name'])
            ->whereDate('created_at', '>=', $now->subDay())
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

            foreach ($recentCreatedTasks as $task) {
            $recentActivities[] = [
                'type' => 'task_created',
                'title' => 'Nouvelle tâche',
                'description' => $task->title . ' créée par ' . ($task->creator ? $task->creator->name : 'Inconnu'),
                'time' => $task->created_at
            ];
            }

            // Objectifs récemment atteints
            $recentCompletedObjectives = Objective::whereIn('project_id', function($query) use ($team) {
            $query->select('id')
                ->from('projects')
                ->where('team_id', $team->id);
            })
            ->where('is_completed', true)
            ->with(['project:id,name', 'creator:id,name'])
            ->whereDate('completed_at', '>=', $now->subDay())
            ->orderBy('completed_at', 'desc')
            ->take(5)
            ->get();

            foreach ($recentCompletedObjectives as $objective) {
            $recentActivities[] = [
                'type' => 'objective_completed',
                'title' => 'Objectif atteint',
                'description' => $objective->title . ' du projet ' . ($objective->project ? $objective->project->name : 'Inconnu'),
                'time' => $objective->completed_at
            ];
            }

            // Nouveaux projets créés
            $recentCreatedProjects = Project::where('team_id', $team->id)
            ->with(['creator:id,name'])
            ->whereDate('created_at', '>=', $now->subDay())
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

            foreach ($recentCreatedProjects as $project) {
            $recentActivities[] = [
                'type' => 'project_created',
                'title' => 'Nouveau projet',
                'description' => $project->name . ' créé par ' . ($project->creator ? $project->creator->name : 'Inconnu'),
                'time' => $project->created_at
            ];
            }

            // Trier les activités par date (la plus récente en premier)
            usort($recentActivities, function ($a, $b) {
            return strtotime($b['time']) - strtotime($a['time']);
            });

            // Limiter à 10 activités
            $recentActivities = array_slice($recentActivities, 0, 10);

            // Regrouper toutes les données du dashboard
            $dashboardData = [
                'activeProjects' => $activeProjects,
                'inProgressTasks' => $inProgressTasks,
                'pendingObjectives' => $pendingObjectives,
                'completionRate' => $completionRate,
                'recentProjects' => $recentProjects,
                'teamPerformance' => $teamPerformance,
                'urgentTasks' => $urgentTasks,
                'recentActivities' => $recentActivities,
                // Ajout des données de présence directement ici
                'presenceData' => [
                    'summary' => $presenceData,
                    'period' => [
                        'start' => Carbon::now()->subDays(7)->format('Y-m-d'),
                        'end' => Carbon::now()->format('Y-m-d'),
                        'workingDays' => $workingDays
                    ]
                ]
            ];

            return Inertia::render('Manager/Dashboard', [
                'team' => $team->only('id', 'name'),
                'dashboardData' => $dashboardData
            ]);

// Ajouter dans votre contrôleur
$lastDays = collect(range(0, 6))->map(function($day) use ($now) {
    return $now->copy()->subDays($day)->format('Y-m-d');
})->reverse()->values()->all();

$progressData = [];

foreach ($lastDays as $day) {
    $date = Carbon::parse($day);

    $completedTasks = Task::where('team_id', $team->id)
        ->where('status', 'completed')
        ->whereDate('end_time', $day)
        ->count();

    $progressData[] = [
        'x' => $date->timestamp * 1000, // Pour JavaScript Date
        'y' => $completedTasks
    ];
}

$dashboardData['progressChartData'] = [
    'items' => $progressData,
    'xLabel' => 'Date',
    'yLabel' => 'Tâches terminées',
    'title' => 'Tâches terminées par jour',
    'xType' => 'date'
];

            }

/**
 * Afficher le tableau de bord des collaborateurs
 */
public function collaborateurDashboard(Request $request)
{
    $user = $request->user();

    // Vérifier que l'utilisateur est bien un collaborateur
    if ($user->role !== 'collaborateur') {
        return redirect()->route('access.denied')->with('error', 'Accès non autorisé');
    }

    // Équipes auxquelles appartient l'utilisateur
    $teamIds = $user->allTeams()->pluck('id')->toArray();

    // Statistiques des tâches
    $totalTasks = Task::whereIn('team_id', $teamIds)
        ->where('assigned_to', $user->id)
        ->count();

    $completedTasks = Task::whereIn('team_id', $teamIds)
        ->where('assigned_to', $user->id)
        ->where('status', 'completed')
        ->count();

    $inProgressTasks = Task::whereIn('team_id', $teamIds)
        ->where('assigned_to', $user->id)
        ->where('status', 'in_progress')
        ->count();

    $pendingTasks = Task::whereIn('team_id', $teamIds)
        ->where('assigned_to', $user->id)
        ->where('status', 'pending')
        ->count();

    $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

    // Message personnalisé pour la journée
    $dayMessage = $this->getDayMessage($pendingTasks, $inProgressTasks);

    // Tâches dues aujourd'hui
    $tasksDueToday = Task::whereIn('team_id', $teamIds)
        ->where('assigned_to', $user->id)
        ->where('status', 'pending')
        ->where(function($query) {
            $query->whereDate('created_at', '<=', Carbon::now()->subDays(5))
                ->orWhere(function($query) {
                    // Considérer les tâches qui ont spécifiquement une date d'échéance aujourd'hui
                    // Cette partie est à adapter si vous avez un champ date d'échéance
                });
        })
        ->count();

    // Tâches prioritaires (en attente, triées par ancienneté)
    $priorityTasks = Task::whereIn('team_id', $teamIds)
        ->where('assigned_to', $user->id)
        ->where('status', 'pending')
        ->with(['project:id,name'])
        ->orderBy('created_at', 'asc')
        ->take(5)
        ->get()
        ->map(function($task) {
            // Calculer depuis combien de jours la tâche est en attente
            $daysPending = Carbon::parse($task->created_at)->diffInDays();

            return [
                'id' => $task->id,
                'title' => $task->title,
                'project' => $task->project ? $task->project->name : 'N/A',
                'created_at' => $task->created_at,
                'daysPending' => $daysPending
            ];
        });

    // Tâches en cours avec durée
    $inProgressTaskList = Task::whereIn('team_id', $teamIds)
        ->where('assigned_to', $user->id)
        ->where('status', 'in_progress')
        ->whereNotNull('start_time')
        ->with(['project:id,name'])
        ->orderBy('start_time', 'asc')
        ->take(5)
        ->get()
        ->map(function($task) {
            // Calculer la durée depuis le début de la tâche
            $startTime = Carbon::parse($task->start_time);
            $now = Carbon::now();
            $durationMinutes = $startTime->diffInMinutes($now);

            return [
                'id' => $task->id,
                'title' => $task->title,
                'project' => $task->project ? $task->project->name : 'N/A',
                'startTime' => $task->start_time,
                'duration' => $durationMinutes
            ];
        });

    // Projets auxquels l'utilisateur est assigné
    $projects = $this->getProjectsWithTaskStats($user->id, $teamIds);

    // Statistiques personnelles
    $stats = $this->getUserStats($user->id, $teamIds);

    // Données pour le graphique de performance
    $performanceChartData = $this->getPerformanceChartData($user->id, $teamIds);

    // Assembler les données du dashboard
    $dashboardData = [
        'totalTasks' => $totalTasks,
        'completedTasks' => $completedTasks,
        'inProgressTasks' => $inProgressTasks,
        'pendingTasks' => $pendingTasks,
        'completionRate' => $completionRate,
        'dayMessage' => $dayMessage,
        'tasksDueToday' => $tasksDueToday,
        'priorityTasks' => $priorityTasks,
        'inProgressTaskList' => $inProgressTaskList,
        'projects' => $projects,
        'stats' => $stats,
        'performanceChartData' => $performanceChartData
    ];

 // Ajouter les alertes de tâches en retard
 $delayedTasks = ProductivityAlert::getCollaborateurDelayedTasks($user->id);

 // Assembler les données du dashboard
 $dashboardData = [
     // Données existantes...
     'totalTasks' => $totalTasks,
     'completedTasks' => $completedTasks,
     'inProgressTasks' => $inProgressTasks,
     'pendingTasks' => $pendingTasks,
     'completionRate' => $completionRate,
     'dayMessage' => $dayMessage,
     'tasksDueToday' => $tasksDueToday,
     'priorityTasks' => $priorityTasks,
     'inProgressTaskList' => $inProgressTaskList,
     'projects' => $projects,
     'stats' => $stats,
     'performanceChartData' => $performanceChartData,

     // Ajout des tâches en retard
     'delayedTasks' => $delayedTasks,
     'hasDelayedTasks' => $delayedTasks->count() > 0,
     'alertThreshold' => ProductivityAlert::ALERT_THRESHOLD_DAYS
 ];

    return Inertia::render('Collaborateur/Dashboard', [
        'user' => $user->only('id', 'name', 'email'),
        'dashboardData' => $dashboardData
    ]);
}

/**
 * Obtenir un message personnalisé selon la situation de travail
 */
private function getDayMessage($pendingTasks, $inProgressTasks)
{
    if ($inProgressTasks > 0) {
        return "Vous avez {$inProgressTasks} tâche" . ($inProgressTasks > 1 ? 's' : '') . " en cours";
    }

    if ($pendingTasks > 0) {
        return "Vous avez {$pendingTasks} tâche" . ($pendingTasks > 1 ? 's' : '') . " en attente";
    }

    $dayOfWeek = Carbon::now()->dayOfWeek;

    // Messages selon le jour de la semaine
    if ($dayOfWeek == Carbon::MONDAY) {
        return "Bienvenue pour une nouvelle semaine productive !";
    } elseif ($dayOfWeek == Carbon::FRIDAY) {
        return "C'est vendredi, bientôt le week-end !";
    } else {
        $messages = [
            "Belle journée pour être productif !",
            "Prêt à relever de nouveaux défis ?",
            "Faisons de cette journée un succès !",
            "Une journée sans tâche est une journée réussie !"
        ];

        return $messages[array_rand($messages)];
    }
}

/**
 * Obtenir les projets avec leurs statistiques de tâches
 */
private function getProjectsWithTaskStats($userId, $teamIds)
{
    // Trouver tous les projets qui ont des tâches assignées à cet utilisateur
    $projectIds = Task::whereIn('team_id', $teamIds)
        ->where('assigned_to', $userId)
        ->pluck('project_id')
        ->unique()
        ->filter()
        ->values()
        ->all();

    $projects = Project::whereIn('id', $projectIds)
        ->get()
        ->map(function($project) use ($userId) {
            // Calculer les statistiques des tâches pour ce projet et cet utilisateur
            $totalTasks = Task::where('project_id', $project->id)
                ->where('assigned_to', $userId)
                ->count();

            $tasksCompleted = Task::where('project_id', $project->id)
                ->where('assigned_to', $userId)
                ->where('status', 'completed')
                ->count();

            $completionRate = $totalTasks > 0 ? round(($tasksCompleted / $totalTasks) * 100) : 0;

            return [
                'id' => $project->id,
                'name' => $project->name,
                'description' => $project->description,
                'status' => $project->status,
                'totalTasks' => $totalTasks,
                'tasksCompleted' => $tasksCompleted,
                'completionRate' => $completionRate
            ];
        })
        ->sortByDesc('completionRate')
        ->values()
        ->all();

    return $projects;
}

/**
 * Obtenir les statistiques de l'utilisateur
 */
private function getUserStats($userId, $teamIds)
{
    // Temps moyen par tâche terminée
    $completedTasks = Task::whereIn('team_id', $teamIds)
        ->where('assigned_to', $userId)
        ->where('status', 'completed')
        ->whereNotNull('start_time')
        ->whereNotNull('end_time')
        ->get();

    $totalDuration = 0;
    foreach ($completedTasks as $task) {
        $startTime = Carbon::parse($task->start_time);
        $endTime = Carbon::parse($task->end_time);
        $totalDuration += $startTime->diffInMinutes($endTime);
    }

    $averageTimePerTask = $completedTasks->count() > 0 ? $totalDuration / $completedTasks->count() : 0;

    // Tâches par jour (cette semaine)
    $startOfWeek = Carbon::now()->startOfWeek();
    $tasksDoneThisWeek = Task::whereIn('team_id', $teamIds)
        ->where('assigned_to', $userId)
        ->where('status', 'completed')
        ->whereDate('end_time', '>=', $startOfWeek)
        ->count();

    $daysSinceStartOfWeek = Carbon::now()->diffInDays($startOfWeek) + 1;
    $tasksPerDay = $daysSinceStartOfWeek > 0 ? $tasksDoneThisWeek / $daysSinceStartOfWeek : 0;

    // Taux de complétion hebdomadaire
    $tasksAssignedThisWeek = Task::whereIn('team_id', $teamIds)
        ->where('assigned_to', $userId)
        ->whereDate('created_at', '>=', $startOfWeek)
        ->count();

    $weeklyCompletionRate = $tasksAssignedThisWeek > 0 ? round(($tasksDoneThisWeek / $tasksAssignedThisWeek) * 100) : 0;

    return [
        'averageTimePerTask' => round($averageTimePerTask),
        'tasksPerDay' => $tasksPerDay,
        'weeklyCompletionRate' => $weeklyCompletionRate
    ];
}

/**
 * Obtenir les données pour le graphique de performance
 */
private function getPerformanceChartData($userId, $teamIds)
{
    // Récupérer les 7 dernières tâches terminées
    $recentTasks = Task::whereIn('team_id', $teamIds)
        ->where('assigned_to', $userId)
        ->where('status', 'completed')
        ->whereNotNull('start_time')
        ->whereNotNull('end_time')
        ->orderBy('end_time', 'desc')
        ->take(7)
        ->get();

    $chartData = [];

    foreach ($recentTasks as $task) {
        $startTime = Carbon::parse($task->start_time);
        $endTime = Carbon::parse($task->end_time);
        $duration = $startTime->diffInMinutes($endTime);

        $chartData[] = [
            'x' => $endTime->timestamp * 1000, // Pour JavaScript Date
            'y' => $duration
        ];
    }

    // Inverser pour avoir l'ordre chronologique
    $chartData = array_reverse($chartData);

    return [
        'items' => $chartData,
        'xType' => 'date'
    ];
}

    /**
     * Compte le nombre de jours ouvrés (Lundi-Vendredi) entre deux dates
     */
    private function countWorkingDays(Carbon $startDate, Carbon $endDate)
    {
        $days = 0;
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            // 0 = dimanche, 6 = samedi
            if (!in_array($currentDate->dayOfWeek, [0, 6])) {
                $days++;
            }
            $currentDate->addDay();
        }

        return $days;
    }
}
