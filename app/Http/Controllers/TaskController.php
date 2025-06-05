<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Team;
use App\Models\User;
use App\Models\Project;
use App\Models\Objective;
use App\Http\Controllers\SlackController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Inertia\Inertia;

class TaskController extends Controller
{
    use AuthorizesRequests;

    // Pour les managers - afficher et gérer toutes les tâches
    public function managerTasks(Request $request)
    {
        $user = $request->user();
        $team = $user->currentTeam;

        $tasks = Task::where('team_id', $team->id)
            ->with(['assignedTo:id,name,email', 'creator:id,name'])
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Manager/Tasks', [
            'tasks' => $tasks,
            'team' => $team,
        ]);
    }

    public function collaborateurTasks(Request $request)
    {
        $user = $request->user();

        // Récupérer les IDs de toutes les équipes auxquelles l'utilisateur appartient
        $teamIds = $user->allTeams()->pluck('id')->toArray();

        // Récupérer toutes les tâches assignées à l'utilisateur quelle que soit l'équipe
        $tasks = Task::whereIn('team_id', $teamIds)
            ->where('assigned_to', $user->id)
            ->with(['creator:id,name', 'team:id,name'])
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Collaborateur/Tasks', [
            'tasks' => $tasks,
            'teams' => $user->allTeams()
        ]);
    }

    // Création de tâche - uniquement pour les managers
    public function create(Request $request)
    {
        $team = $request->user()->currentTeam;

        // Récupérer uniquement les membres de type collaborateur de cette équipe
        $teamMembers = $team->users
            ->filter(function ($user) {
                return $user->role === 'collaborateur';
            })
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ];
            });

        // Récupérer les projets de l'équipe
        $projects = Project::where('team_id', $team->id)
            ->where('status', 'active')
            ->get(['id', 'name']);

        return Inertia::render('Manager/TaskCreate', [
            'team' => $team,
            'teamMembers' => $teamMembers,
            'projects' => $projects,
        ]);
    }

    // Enregistrement d'une nouvelle tâche
    public function store(Request $request)
    {
        $user = $request->user();
        $team = $user->currentTeam;

        // Valider que l'utilisateur est manager
        if ($user->role !== 'manager') {
            return redirect()->back()->with('error', 'Seuls les managers peuvent créer des tâches.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'required|exists:users,id',
            'project_id' => 'required|exists:projects,id',
        ]);

        // Vérifier que le projet appartient bien à l'équipe
        $project = Project::findOrFail($validated['project_id']);
        if ($project->team_id !== $team->id) {
            return redirect()->back()->with('error', 'Le projet sélectionné n\'appartient pas à votre équipe.');
        }

        $task = Task::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'team_id' => $team->id,
            'project_id' => $validated['project_id'],
            'assigned_to' => $validated['assigned_to'],
            'created_by' => $user->id,
            'status' => 'pending',
        ]);

        SlackController::notifyTaskAssigned($task);

        return redirect()->route('manager.tasks')->with('success', 'Tâche créée avec succès');
    }

    // Afficher les détails d'une tâche (pour les deux rôles)
    public function show(Task $task)
    {
        $user = auth()->user();
        $this->authorize('view', $task);

        $task->load(['assignedTo:id,name,email', 'creator:id,name']);

        return Inertia::render('Tasks/Show', [
            'task' => $task,
            'canEdit' => $user->role === 'manager',
        ]);
    }

    // Démarrer une tâche (pour les collaborateurs)
    public function startTask(Task $task)
    {
        $user = auth()->user();
        $this->authorize('start', $task);

        if ($task->isStarted()) {
            return redirect()->back()->with('error', 'Cette tâche est déjà démarrée');
        }

        $task->update([
            'status' => 'in_progress',
            'start_time' => now(),
        ]);

        return redirect()->back()->with('success', 'Tâche démarrée avec succès');
    }

    // Terminer une tâche (pour les collaborateurs)
    public function completeTask(Task $task)
    {
        $user = auth()->user();
        $this->authorize('complete', $task);

        if ($task->isCompleted()) {
            return redirect()->back()->with('error', 'Cette tâche est déjà terminée');
        }

        // Si la tâche n'a pas été démarrée, on définit le temps de démarrage à maintenant
        if (!$task->start_time) {
            $task->start_time = now();
        }

        $task->update([
            'status' => 'completed',
            'end_time' => now(),
        ]);

        SlackController::notifyTaskCompleted($task);

        return redirect()->back()->with('success', 'Tâche terminée avec succès');
    }

    /**
     * Afficher les KPIs d'un collaborateur
     */
    public function collaborateurKpi(Request $request, User $collaborateur = null)
    {
        $user = $request->user();

        // Si aucun collaborateur n'est spécifié, utilise l'utilisateur connecté
        if (!$collaborateur) {
            $collaborateur = $user;
        }

        // Vérifie les autorisations - un utilisateur peut voir ses propres KPIs,
        // un manager peut voir les KPIs des collaborateurs de son équipe
        if ($user->id !== $collaborateur->id && $user->role !== 'manager') {
            return redirect()->route('access.denied')->with('error', 'Accès non autorisé');
        }

        // Si c'est un manager qui veut voir les KPIs d'un collaborateur,
        // vérifie si ce collaborateur est dans une de ses équipes
        if ($user->role === 'manager' && $user->id !== $collaborateur->id) {
            $isInTeam = false;
            foreach ($user->allTeams() as $team) {
                if ($team->hasUser($collaborateur)) {
                    $isInTeam = true;
                    break;
                }
            }

            if (!$isInTeam) {
                return redirect()->route('access.denied')->with('error', 'Ce collaborateur n\'est pas dans une de vos équipes');
            }
        }

        // Récupère toutes les équipes du collaborateur
        $teamIds = $collaborateur->allTeams()->pluck('id')->toArray();

        // Calcule les KPIs
        $totalTasks = Task::whereIn('team_id', $teamIds)
            ->where('assigned_to', $collaborateur->id)
            ->count();

        $completedTasks = Task::whereIn('team_id', $teamIds)
            ->where('assigned_to', $collaborateur->id)
            ->where('status', 'completed')
            ->count();

        $inProgressTasks = Task::whereIn('team_id', $teamIds)
            ->where('assigned_to', $collaborateur->id)
            ->where('status', 'in_progress')
            ->count();

        $pendingTasks = Task::whereIn('team_id', $teamIds)
            ->where('assigned_to', $collaborateur->id)
            ->where('status', 'pending')
            ->count();

        // Calcul du temps moyen par tâche terminée
        $completedTasksData = Task::whereIn('team_id', $teamIds)
            ->where('assigned_to', $collaborateur->id)
            ->where('status', 'completed')
            ->whereNotNull('start_time')
            ->whereNotNull('end_time')
            ->get();

        $totalTimeMinutes = 0;
        foreach ($completedTasksData as $task) {
            $totalTimeMinutes += $task->getDurationAttribute();
        }

        $averageTimePerTask = $completedTasks > 0 ? $totalTimeMinutes / $completedTasks : 0;

        // Récupère les 5 dernières tâches terminées
        $recentCompletedTasks = Task::whereIn('team_id', $teamIds)
            ->where('assigned_to', $collaborateur->id)
            ->where('status', 'completed')
            ->with('team:id,name')
            ->orderBy('end_time', 'desc')
            ->take(5)
            ->get();

        // Statistiques par semaine (pour un graphique de tendance)
        $weeklyStats = Task::whereIn('team_id', $teamIds)
            ->where('assigned_to', $collaborateur->id)
            ->where('status', 'completed')
            ->whereNotNull('end_time')
            ->selectRaw('YEARWEEK(end_time) as week, COUNT(*) as count')
            ->groupBy('week')
            ->orderBy('week', 'desc')
            ->take(10)
            ->get()
            ->reverse();

        // Statistiques par équipe
        $teamStats = Task::whereIn('team_id', $teamIds)
            ->where('assigned_to', $collaborateur->id)
            ->where('status', 'completed')
            ->selectRaw('team_id, COUNT(*) as count')
            ->groupBy('team_id')
            ->get()
            ->map(function ($item) {
                $team = Team::find($item->team_id);
                return [
                    'team_name' => $team ? $team->name : 'Équipe inconnue',
                    'count' => $item->count
                ];
            });

        return Inertia::render('Collaborateur/Kpi', [
            'collaborateur' => $collaborateur->only('id', 'name', 'email'),
            'kpiData' => [
                'totalTasks' => $totalTasks,
                'completedTasks' => $completedTasks,
                'inProgressTasks' => $inProgressTasks,
                'pendingTasks' => $pendingTasks,
                'completionRate' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0,
                'averageTimeMinutes' => round($averageTimePerTask, 0),
                'recentCompletedTasks' => $recentCompletedTasks,
                'weeklyStats' => $weeklyStats,
                'teamStats' => $teamStats
            ],
            'isManager' => $user->role === 'manager' && $user->id !== $collaborateur->id,
        ]);
    }

    /**
     * Afficher les KPIs de l'équipe (pour le manager)
     */
    public function teamKpi(Request $request)
    {
        $user = $request->user();

        // Vérifier que l'utilisateur est bien un manager
        if ($user->role !== 'manager') {
            return redirect()->route('access.denied')->with('error', 'Accès non autorisé');
        }

        $team = $user->currentTeam;

        // Récupérer tous les collaborateurs de l'équipe
        $collaborateurs = $team->users->filter(function ($teamUser) {
            return $teamUser->role === 'collaborateur';
        });

        // Collecter les KPIs par collaborateur
        $collaborateurKpis = [];

        foreach ($collaborateurs as $collaborateur) {
            // Tâches totales assignées à ce collaborateur dans cette équipe
            $totalTasks = Task::where('team_id', $team->id)
                ->where('assigned_to', $collaborateur->id)
                ->count();

            // Tâches terminées
            $completedTasks = Task::where('team_id', $team->id)
                ->where('assigned_to', $collaborateur->id)
                ->where('status', 'completed')
                ->count();

            // Calcul du temps moyen par tâche terminée
            $completedTasksData = Task::where('team_id', $team->id)
                ->where('assigned_to', $collaborateur->id)
                ->where('status', 'completed')
                ->whereNotNull('start_time')
                ->whereNotNull('end_time')
                ->get();

            $totalTimeMinutes = 0;
            foreach ($completedTasksData as $task) {
                $totalTimeMinutes += $task->getDurationAttribute();
            }

            $averageTimePerTask = $completedTasks > 0 ? $totalTimeMinutes / $completedTasks : 0;

            // Ajouter aux résultats
            $collaborateurKpis[] = [
                'collaborateur' => $collaborateur->only('id', 'name', 'email'),
                'totalTasks' => $totalTasks,
                'completedTasks' => $completedTasks,
                'completionRate' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0,
                'averageTimeMinutes' => round($averageTimePerTask, 0),
            ];
        }

        // Trier par taux de complétion (du plus haut au plus bas)
        usort($collaborateurKpis, function ($a, $b) {
            return $b['completionRate'] <=> $a['completionRate'];
        });

        // Récupérer les statistiques globales de l'équipe pour les tâches
        $teamTotalTasks = Task::where('team_id', $team->id)->count();
        $teamCompletedTasks = Task::where('team_id', $team->id)->where('status', 'completed')->count();
        $teamPendingTasks = Task::where('team_id', $team->id)->where('status', 'pending')->count();
        $teamInProgressTasks = Task::where('team_id', $team->id)->where('status', 'in_progress')->count();

        // Récupérer les dernières tâches terminées (toute l'équipe)
        $recentCompletedTasks = Task::where('team_id', $team->id)
            ->where('status', 'completed')
            ->with(['assignedTo:id,name,email'])
            ->orderBy('end_time', 'desc')
            ->take(10)
            ->get();

        // Statistiques par semaine pour les tâches
        $weeklyStats = Task::where('team_id', $team->id)
            ->where('status', 'completed')
            ->whereNotNull('end_time')
            ->selectRaw('YEARWEEK(end_time) as week, COUNT(*) as count')
            ->groupBy('week')
            ->orderBy('week', 'desc')
            ->take(10)
            ->get()
            ->reverse();

        // Récupérer tous les projets de l'équipe
        $projects = Project::where('team_id', $team->id)
            ->with(['objectives.creator'])
            ->get();

        // Calculer les statistiques globales pour les objectifs
        $teamTotalObjectives = 0;
        $teamCompletedObjectives = 0;

        foreach ($projects as $project) {
            $teamTotalObjectives += $project->objectives->count();
            $teamCompletedObjectives += $project->objectives->where('is_completed', true)->count();
        }

        $objectivesCompletionRate = $teamTotalObjectives > 0
            ? round(($teamCompletedObjectives / $teamTotalObjectives) * 100, 1)
            : 0;

        // Calculer la moyenne d'objectifs par projet
        $objectivesPerProject = $projects->count() > 0
            ? round($teamTotalObjectives / $projects->count(), 1)
            : 0;

        // Statistiques d'objectifs par projet
        $projectsObjectivesStats = $projects->map(function ($project) {
            $totalObjectives = $project->objectives->count();
            $completedObjectives = $project->objectives->where('is_completed', true)->count();

            return [
                'id' => $project->id,
                'name' => $project->name,
                'totalObjectives' => $totalObjectives,
                'completedObjectives' => $completedObjectives,
                'completionRate' => $totalObjectives > 0
                    ? round(($completedObjectives / $totalObjectives) * 100)
                    : 0
            ];
        })->sortByDesc('completionRate')->values()->all();

        // Récupérer les objectifs récemment complétés
        $recentCompletedObjectives = Objective::whereIn('project_id', $projects->pluck('id'))
            ->where('is_completed', true)
            ->with(['project:id,name', 'creator:id,name'])
            ->orderBy('completed_at', 'desc')
            ->take(10)
            ->get();

        // Statistiques hebdomadaires des objectifs complétés
        $weeklyObjectivesStats = DB::table('objectives')
            ->selectRaw('YEARWEEK(completed_at) as week, COUNT(*) as count')
            ->whereIn('project_id', $projects->pluck('id'))
            ->where('is_completed', true)
            ->whereNotNull('completed_at')
            ->groupBy('week')
            ->orderBy('week', 'desc')
            ->take(10)
            ->get()
            ->reverse();

        return Inertia::render('Manager/TeamKpi', [
            'team' => $team->only('id', 'name'),
            'collaborateurKpis' => $collaborateurKpis,
            'teamKpiData' => [
                // Données pour les tâches
                'totalTasks' => $teamTotalTasks,
                'completedTasks' => $teamCompletedTasks,
                'pendingTasks' => $teamPendingTasks,
                'inProgressTasks' => $teamInProgressTasks,
                'completionRate' => $teamTotalTasks > 0 ? round(($teamCompletedTasks / $teamTotalTasks) * 100, 1) : 0,
                'recentCompletedTasks' => $recentCompletedTasks,
                'weeklyStats' => $weeklyStats,

                // Données pour les objectifs
                'totalObjectives' => $teamTotalObjectives,
                'completedObjectives' => $teamCompletedObjectives,
                'objectivesCompletionRate' => $objectivesCompletionRate,
                'objectivesPerProject' => $objectivesPerProject,
                'projectsObjectivesStats' => $projectsObjectivesStats,
                'recentCompletedObjectives' => $recentCompletedObjectives,
                'weeklyObjectivesStats' => $weeklyObjectivesStats,
            ],
        ]);
    }

    /**
     * Exporter les KPIs d'un collaborateur au format PDF
     */
    public function exportCollaborateurKpiPdf(Request $request, User $collaborateur = null)
    {
        $user = $request->user();

        // Si aucun collaborateur n'est spécifié, utilise l'utilisateur connecté
        if (!$collaborateur) {
            $collaborateur = $user;
        }

        // Vérifie les autorisations - un utilisateur peut voir ses propres KPIs,
        // un manager peut voir les KPIs des collaborateurs de son équipe
        if ($user->id !== $collaborateur->id && $user->role !== 'manager') {
            return redirect()->route('access.denied')->with('error', 'Accès non autorisé');
        }

        // Si c'est un manager qui veut voir les KPIs d'un collaborateur,
        // vérifie si ce collaborateur est dans une de ses équipes
        if ($user->role === 'manager' && $user->id !== $collaborateur->id) {
            $isInTeam = false;
            foreach ($user->allTeams() as $team) {
                if ($team->hasUser($collaborateur)) {
                    $isInTeam = true;
                    break;
                }
            }

            if (!$isInTeam) {
                return redirect()->route('access.denied')->with('error', 'Ce collaborateur n\'est pas dans une de vos équipes');
            }
        }

        // Récupère toutes les équipes du collaborateur
        $teamIds = $collaborateur->allTeams()->pluck('id')->toArray();

        // Calcule les KPIs
        $totalTasks = Task::whereIn('team_id', $teamIds)
            ->where('assigned_to', $collaborateur->id)
            ->count();

        $completedTasks = Task::whereIn('team_id', $teamIds)
            ->where('assigned_to', $collaborateur->id)
            ->where('status', 'completed')
            ->count();

        $inProgressTasks = Task::whereIn('team_id', $teamIds)
            ->where('assigned_to', $collaborateur->id)
            ->where('status', 'in_progress')
            ->count();

        $pendingTasks = Task::whereIn('team_id', $teamIds)
            ->where('assigned_to', $collaborateur->id)
            ->where('status', 'pending')
            ->count();

        // Calcul du temps moyen par tâche terminée
        $completedTasksData = Task::whereIn('team_id', $teamIds)
            ->where('assigned_to', $collaborateur->id)
            ->where('status', 'completed')
            ->whereNotNull('start_time')
            ->whereNotNull('end_time')
            ->get();

        $totalTimeMinutes = 0;
        foreach ($completedTasksData as $task) {
            $totalTimeMinutes += $task->getDurationAttribute();
        }

        $averageTimePerTask = $completedTasks > 0 ? $totalTimeMinutes / $completedTasks : 0;

        // Récupère les 10 dernières tâches terminées
        $recentCompletedTasks = Task::whereIn('team_id', $teamIds)
            ->where('assigned_to', $collaborateur->id)
            ->where('status', 'completed')
            ->with('team:id,name')
            ->orderBy('end_time', 'desc')
            ->take(10)
            ->get();

        // Préparer les données pour le PDF
        $data = [
            'collaborateur' => $collaborateur,
            'kpiData' => [
                'totalTasks' => $totalTasks,
                'completedTasks' => $completedTasks,
                'inProgressTasks' => $inProgressTasks,
                'pendingTasks' => $pendingTasks,
                'completionRate' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0,
                'averageTimeMinutes' => round($averageTimePerTask, 0),
                'recentCompletedTasks' => $recentCompletedTasks,
            ],
            'dateGeneration' => now()->format('d/m/Y H:i'),
        ];

        // Générer le PDF
        $pdf = app('dompdf.wrapper');
        $pdf->loadView('pdf.collaborateur-kpi', $data);
        $pdf->setPaper('a4', 'portrait');

        // Nom du fichier
        $filename = 'kpi-' . $collaborateur->name . '-' . now()->format('Y-m-d') . '.pdf';

        // Enregistrer temporairement le fichier
        $tempPath = storage_path('app/public/temp/' . $filename);

        // Créer le répertoire s'il n'existe pas
        if (!file_exists(storage_path('app/public/temp'))) {
            mkdir(storage_path('app/public/temp'), 0775, true);
        }

        file_put_contents($tempPath, $pdf->output());

        // Servir le fichier comme un téléchargement
        return response()->download($tempPath, $filename, [
            'Content-Type' => 'application/pdf',
        ])->deleteFileAfterSend(true);
    }

/**
 * Exporter les KPIs de l'équipe au format PDF
 */
    public function exportTeamKpiPdf(Request $request)
    {
        $user = $request->user();

        // Vérifier que l'utilisateur est bien un manager
        if ($user->role !== 'manager') {
            return redirect()->route('access.denied')->with('error', 'Accès non autorisé');
        }

        $team = $user->currentTeam;

        // Récupérer tous les collaborateurs de l'équipe
        $collaborateurs = $team->users->filter(function ($teamUser) {
            return $teamUser->role === 'collaborateur';
        });

        // Collecter les KPIs par collaborateur
        $collaborateurKpis = [];

        foreach ($collaborateurs as $collaborateur) {
            // Tâches totales assignées à ce collaborateur dans cette équipe
            $totalTasks = Task::where('team_id', $team->id)
                ->where('assigned_to', $collaborateur->id)
                ->count();

            // Tâches terminées
            $completedTasks = Task::where('team_id', $team->id)
                ->where('assigned_to', $collaborateur->id)
                ->where('status', 'completed')
                ->count();

            // Calcul du temps moyen par tâche terminée
            $completedTasksData = Task::where('team_id', $team->id)
                ->where('assigned_to', $collaborateur->id)
                ->where('status', 'completed')
                ->whereNotNull('start_time')
                ->whereNotNull('end_time')
                ->get();

            $totalTimeMinutes = 0;
            foreach ($completedTasksData as $task) {
                $totalTimeMinutes += $task->getDurationAttribute();
            }

            $averageTimePerTask = $completedTasks > 0 ? $totalTimeMinutes / $completedTasks : 0;

            // Ajouter aux résultats
            $collaborateurKpis[] = [
                'collaborateur' => $collaborateur,
                'totalTasks' => $totalTasks,
                'completedTasks' => $completedTasks,
                'completionRate' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0,
                'averageTimeMinutes' => round($averageTimePerTask, 0),
            ];
        }

        // Trier par taux de complétion (du plus haut au plus bas)
        usort($collaborateurKpis, function ($a, $b) {
            return $b['completionRate'] <=> $a['completionRate'];
        });

        // Récupérer les statistiques globales de l'équipe pour les tâches
        $teamTotalTasks = Task::where('team_id', $team->id)->count();
        $teamCompletedTasks = Task::where('team_id', $team->id)->where('status', 'completed')->count();
        $teamPendingTasks = Task::where('team_id', $team->id)->where('status', 'pending')->count();
        $teamInProgressTasks = Task::where('team_id', $team->id)->where('status', 'in_progress')->count();

        // Récupérer les dernières tâches terminées (toute l'équipe)
        $recentCompletedTasks = Task::where('team_id', $team->id)
            ->where('status', 'completed')
            ->with(['assignedTo:id,name,email'])
            ->orderBy('end_time', 'desc')
            ->take(10)
            ->get();

        // Récupérer tous les projets de l'équipe
        $projects = Project::where('team_id', $team->id)
            ->with(['objectives.creator', 'objectives.project'])
            ->get();

        // Calculer les statistiques globales pour les objectifs
        $teamTotalObjectives = 0;
        $teamCompletedObjectives = 0;

        foreach ($projects as $project) {
            $teamTotalObjectives += $project->objectives->count();
            $teamCompletedObjectives += $project->objectives->where('is_completed', true)->count();
        }

        $objectivesCompletionRate = $teamTotalObjectives > 0
            ? round(($teamCompletedObjectives / $teamTotalObjectives) * 100, 1)
            : 0;

        // Calculer la moyenne d'objectifs par projet
        $objectivesPerProject = $projects->count() > 0
            ? round($teamTotalObjectives / $projects->count(), 1)
            : 0;

        // Statistiques d'objectifs par projet
        $projectsObjectivesStats = $projects->map(function ($project) {
            $totalObjectives = $project->objectives->count();
            $completedObjectives = $project->objectives->where('is_completed', true)->count();

            return [
                'id' => $project->id,
                'name' => $project->name,
                'totalObjectives' => $totalObjectives,
                'completedObjectives' => $completedObjectives,
                'completionRate' => $totalObjectives > 0
                    ? round(($completedObjectives / $totalObjectives) * 100)
                    : 0
            ];
        })->sortByDesc('completionRate')->values()->all();

        // Récupérer les objectifs récemment complétés
        $recentCompletedObjectives = Objective::whereIn('project_id', $projects->pluck('id'))
            ->where('is_completed', true)
            ->with(['project:id,name', 'creator:id,name'])
            ->orderBy('completed_at', 'desc')
            ->take(10)
            ->get();

        // Préparer les données pour le PDF
        $data = [
            'team' => $team,
            'collaborateurKpis' => $collaborateurKpis,
            'teamKpiData' => [
                // Données pour les tâches
                'totalTasks' => $teamTotalTasks,
                'completedTasks' => $teamCompletedTasks,
                'pendingTasks' => $teamPendingTasks,
                'inProgressTasks' => $teamInProgressTasks,
                'completionRate' => $teamTotalTasks > 0 ? round(($teamCompletedTasks / $teamTotalTasks) * 100, 1) : 0,
                'recentCompletedTasks' => $recentCompletedTasks,

                // Données pour les objectifs
                'totalObjectives' => $teamTotalObjectives,
                'completedObjectives' => $teamCompletedObjectives,
                'objectivesCompletionRate' => $objectivesCompletionRate,
                'objectivesPerProject' => $objectivesPerProject,
                'projectsObjectivesStats' => $projectsObjectivesStats,
                'recentCompletedObjectives' => $recentCompletedObjectives,
            ],
            'dateGeneration' => now()->format('d/m/Y H:i'),
        ];

        // Générer le PDF
        $pdf = app('dompdf.wrapper');
        $pdf->loadView('pdf.team-kpi', $data);
        $pdf->setPaper('a4', 'portrait');

        // Nom du fichier
        $filename = 'kpi-equipe-' . $team->name . '-' . now()->format('Y-m-d') . '.pdf';

        // Enregistrer temporairement le fichier
        $tempPath = storage_path('app/public/temp/' . $filename);

        // Créer le répertoire s'il n'existe pas
        if (!file_exists(storage_path('app/public/temp'))) {
            mkdir(storage_path('app/public/temp'), 0775, true);
        }

        file_put_contents($tempPath, $pdf->output());

        // Servir le fichier comme un téléchargement
        return response()->download($tempPath, $filename, [
            'Content-Type' => 'application/pdf',
        ])->deleteFileAfterSend(true);
    }
}
