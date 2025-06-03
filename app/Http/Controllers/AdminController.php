<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Team;
use App\Models\Task;
use App\Models\Project;
use App\Models\Objective;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;
use Inertia\Inertia;

class AdminController extends Controller
{
    /************************************
     * DASHBOARD ET FONCTIONS ASSOCIÉES
     ************************************/

    /**
     * Affiche le tableau de bord administrateur avec les statistiques générales
     */
    public function dashboard(Request $request)
    {
        // Vérifier que l'utilisateur est bien un administrateur
        if ($request->user()->role !== 'administrateur') {
            return redirect()->route('access.denied')->with('error', 'Accès non autorisé');
        }

        // Statistiques sur les utilisateurs
        $userStats = [
            'total' => User::count(),
            'byRole' => [
                'administrateur' => User::where('role', 'administrateur')->count(),
                'manager' => User::where('role', 'manager')->count(),
                'collaborateur' => User::where('role', 'collaborateur')->count(),
            ],
            'recentlyRegistered' => User::orderBy('created_at', 'desc')
                ->take(3)
                ->get()
                ->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                        'created_at' => $user->created_at->format('d/m/Y H:i'),
                    ];
                }),
        ];

        // Statistiques sur les équipes
        $teamStats = [
            'total' => Team::count(),
            'personalTeams' => Team::where('personal_team', true)->count(),
            'organizationTeams' => Team::where('personal_team', false)->count(),
            'recentTeams' => Team::orderBy('created_at', 'desc')
                ->take(3)
                ->get()
                ->map(function ($team) {
                    return [
                        'id' => $team->id,
                        'name' => $team->name,
                        'owner' => $team->owner ? $team->owner->name : 'N/A',
                        'members_count' => $team->users()->count(),
                        'created_at' => $team->created_at->format('d/m/Y'),
                    ];
                })
        ];

        // Statistiques sur les projets
        $projectStats = [
            'total' => Project::count(),
            'byStatus' => [
                'active' => Project::where('status', 'active')->count(),
                'completed' => Project::where('status', 'completed')->count(),
                'on_hold' => Project::where('status', 'on_hold')->count(),
            ],
            'recentProjects' => Project::with('team:id,name', 'creator:id,name')
                ->orderBy('created_at', 'desc')
                ->take(3)
                ->get()
                ->map(function ($project) {
                    return [
                        'id' => $project->id,
                        'name' => $project->name,
                        'team' => $project->team ? $project->team->name : 'N/A',
                        'creator' => $project->creator ? $project->creator->name : 'N/A',
                        'status' => $project->status,
                        'created_at' => $project->created_at->format('d/m/Y'),
                    ];
                }),
        ];

        // Statistiques sur les tâches
        $taskStats = [
            'total' => Task::count(),
            'byStatus' => [
                'pending' => Task::where('status', 'pending')->count(),
                'in_progress' => Task::where('status', 'in_progress')->count(),
                'completed' => Task::where('status', 'completed')->count(),
            ],
            'recentlyCompleted' => Task::where('status', 'completed')
                ->with(['assignedTo:id,name', 'team:id,name'])
                ->orderBy('end_time', 'desc')
                ->take(3)
                ->get()
                ->map(function ($task) {
                    return [
                        'id' => $task->id,
                        'title' => $task->title,
                        'assigned_to' => $task->assignedTo ? $task->assignedTo->name : 'N/A',
                        'team' => $task->team ? $task->team->name : 'N/A',
                        'completed_at' => $task->end_time ? Carbon::parse($task->end_time)->format('d/m/Y H:i') : 'N/A',
                    ];
                }),
        ];

        // Statistiques sur les objectifs
        $objectiveStats = [
            'total' => Objective::count(),
            'completed' => Objective::where('is_completed', true)->count(),
            'pending' => Objective::where('is_completed', false)->count(),
        ];

        // Statistiques d'activité récente (tous types confondus)
        $recentActivity = $this->getRecentActivity();

        // Données d'évolution dans le temps (pour graphiques)
        $timeChartData = $this->getTimeChartData();

        return Inertia::render('Admin/Dashboard', [
            'userStats' => $userStats,
            'teamStats' => $teamStats,
            'projectStats' => $projectStats,
            'taskStats' => $taskStats,
            'objectiveStats' => $objectiveStats,
            'recentActivity' => $recentActivity,
            'timeChartData' => $timeChartData,
        ]);
    }

    /**
     * Récupère l'activité récente dans l'application
     */
    private function getRecentActivity()
    {
        $activity = [];

        // Utilisateurs récemment créés
        $newUsers = User::select('id', 'name', 'created_at')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get()
            ->map(function ($user) {
                return [
                    'type' => 'user_created',
                    'title' => 'Nouvel utilisateur',
                    'description' => "L'utilisateur {$user->name} a été créé",
                    'time' => $user->created_at,
                ];
            });

        // Équipes récemment créées
        $newTeams = Team::select('id', 'name', 'user_id', 'created_at')
            ->with('owner:id,name')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get()
            ->map(function ($team) {
                return [
                    'type' => 'team_created',
                    'title' => 'Nouvelle équipe',
                    'description' => "L'équipe {$team->name} a été créée par " . ($team->owner ? $team->owner->name : 'Utilisateur inconnu'),
                    'time' => $team->created_at,
                ];
            });

        // Projets récemment créés
        $newProjects = Project::select('id', 'name', 'created_by', 'created_at')
            ->with('creator:id,name')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get()
            ->map(function ($project) {
                return [
                    'type' => 'project_created',
                    'title' => 'Nouveau projet',
                    'description' => "Le projet {$project->name} a été créé par " . ($project->creator ? $project->creator->name : 'Utilisateur inconnu'),
                    'time' => $project->created_at,
                ];
            });

        // Tâches récemment complétées
        $completedTasks = Task::select('id', 'title', 'assigned_to', 'end_time')
            ->where('status', 'completed')
            ->whereNotNull('end_time')
            ->with('assignedTo:id,name')
            ->orderBy('end_time', 'desc')
            ->take(3)
            ->get()
            ->map(function ($task) {
                return [
                    'type' => 'task_completed',
                    'title' => 'Tâche terminée',
                    'description' => "La tâche \"{$task->title}\" a été terminée par " . ($task->assignedTo ? $task->assignedTo->name : 'Utilisateur inconnu'),
                    'time' => $task->end_time,
                ];
            });

        // Objectifs récemment atteints
        $completedObjectives = Objective::select('id', 'title', 'created_by', 'completed_at')
            ->where('is_completed', true)
            ->whereNotNull('completed_at')
            ->with('creator:id,name')
            ->orderBy('completed_at', 'desc')
            ->take(3)
            ->get()
            ->map(function ($objective) {
                return [
                    'type' => 'objective_completed',
                    'title' => 'Objectif atteint',
                    'description' => "L'objectif \"{$objective->title}\" a été atteint",
                    'time' => $objective->completed_at,
                ];
            });

        // Combiner toutes les activités et trier par date (la plus récente en premier)
        $activity = collect()
            ->merge($newUsers)
            ->merge($newTeams)
            ->merge($newProjects)
            ->merge($completedTasks)
            ->merge($completedObjectives)
            ->sortByDesc('time')
            ->take(5)
            ->values()
            ->all();

        return $activity;
    }

    /**
     * Récupère les données pour les graphiques d'évolution dans le temps
     */
    private function getTimeChartData()
    {
        // Récupérer les 6 derniers mois
        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $months->push(Carbon::now()->subMonths($i)->format('Y-m'));
        }

        // Utilisateurs par mois
        $usersByMonth = $months->map(function ($month) {
            $date = Carbon::createFromFormat('Y-m', $month);
            $count = User::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            return [
                'month' => $month,
                'count' => $count,
            ];
        });

        // Projets par mois
        $projectsByMonth = $months->map(function ($month) {
            $date = Carbon::createFromFormat('Y-m', $month);
            $count = Project::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            return [
                'month' => $month,
                'count' => $count,
            ];
        });

        // Tâches terminées par mois
        $tasksByMonth = $months->map(function ($month) {
            $date = Carbon::createFromFormat('Y-m', $month);
            $count = Task::where('status', 'completed')
                ->whereYear('end_time', $date->year)
                ->whereMonth('end_time', $date->month)
                ->count();
            return [
                'month' => $month,
                'count' => $count,
            ];
        });

        return [
            'months' => $months,
            'users' => $usersByMonth,
            'projects' => $projectsByMonth,
            'tasks' => $tasksByMonth,
        ];
    }

    /************************************
     * GESTION DES UTILISATEURS
     ************************************/

    /**
     * Liste tous les utilisateurs avec possibilité de filtrer
     */
    public function usersList(Request $request)
    {
        $query = User::query();

        // Filtrage par rôle
        if ($request->has('role') && $request->role !== 'all') {
            $query->where('role', $request->role);
        }

        // Recherche par nom ou email
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // Pagination sans charger les équipes via with()
        $users = $query->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        // Charger les équipes manuellement pour chaque utilisateur
        $users->getCollection()->transform(function ($user) {
            // Ajouter une propriété calculée pour le nombre d'équipes
            $user->teams_count = $user->teams()->count();
            return $user;
        });

        return Inertia::render('Admin/UsersList', [
            'users' => $users,
            'filters' => $request->only(['role', 'search']),
        ]);
    }

    /**
     * Afficher le formulaire de création d'un nouvel utilisateur
     */
    public function createUser()
    {
        // Récupérer toutes les équipes pour l'assignation
        $teams = Team::all(['id', 'name']);

        return Inertia::render('Admin/CreateUser', [
            'teams' => $teams,
        ]);
    }

    /**
     * Enregistrer un nouvel utilisateur
     */
    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:administrateur,manager,collaborateur',
            'teams' => 'nullable|array',
        ]);

        // Créer l'utilisateur
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        // Assigner les équipes si spécifiées
        if (isset($validated['teams']) && is_array($validated['teams'])) {
            foreach ($validated['teams'] as $teamId) {
                $team = Team::find($teamId);
                if ($team) {
                    $team->users()->attach($user, ['role' => $user->role]);
                }
            }
        }

        return redirect()->route('admin.users.list')->with('success', 'Utilisateur créé avec succès');
    }

    /**
     * Afficher le formulaire d'édition d'un utilisateur
     */
    public function editUser(User $user)
    {
        $teams = Team::all()->map(function ($team) {
            return [
                'id' => $team->id,
                'name' => $team->name,
            ];
        });

        return Inertia::render('Admin/EditUser', [
            'user' => $user,
            'userTeams' => $user->teams()->pluck('teams.id'),
            'teams' => $teams,
        ]);
    }

    /**
     * Mettre à jour un utilisateur
     */
    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:administrateur,manager,collaborateur',
            'teams' => 'array',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ]);

        // Mettre à jour les équipes de l'utilisateur
        if (isset($validated['teams'])) {
            foreach (Team::all() as $team) {
                if (in_array($team->id, $validated['teams'])) {
                    if (!$team->users()->where('user_id', $user->id)->exists()) {
                        $team->users()->attach($user, ['role' => $user->role]);
                    }
                } else {
                    if ($team->users()->where('user_id', $user->id)->exists()) {
                        $team->users()->detach($user);
                    }
                }
            }
        }

        return redirect()->route('admin.users.list')->with('success', 'Utilisateur mis à jour avec succès');
    }

    /**
     * Supprimer un utilisateur
     */
    public function destroyUser(User $user)
    {
        // Vérifier qu'on ne supprime pas l'utilisateur actuellement connecté
        if (auth()->id() === $user->id) {
            return redirect()->back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        // Vérifier que l'utilisateur n'est pas le seul administrateur restant
        $adminCount = User::where('role', 'administrateur')->count();
        if ($user->role === 'administrateur' && $adminCount <= 1) {
            return redirect()->back()->with('error', 'Impossible de supprimer le dernier administrateur du système.');
        }

        try {
            DB::beginTransaction();

            // Récupérer toutes les équipes dont l'utilisateur est propriétaire
            $ownedTeams = $user->ownedTeams;

            // Pour chaque équipe dont l'utilisateur est propriétaire
            foreach ($ownedTeams as $team) {
                // Si c'est une équipe personnelle, la supprimer
                if ($team->personal_team) {
                    // Détacher tous les membres de l'équipe
                    $team->users()->detach();

                    // Supprimer l'équipe
                    $team->delete();
                } else {
                    // Sinon, chercher un autre utilisateur pour prendre en charge l'équipe
                    $newOwner = $team->users()
                        ->where('users.id', '!=', $user->id)
                        ->orderByRaw("FIELD(role, 'administrateur', 'manager', 'collaborateur')")
                        ->first();

                    if ($newOwner) {
                        // Transférer la propriété de l'équipe
                        $team->user_id = $newOwner->id;
                        $team->save();
                    } else {
                        // Si aucun autre membre, supprimer l'équipe
                        $team->delete();
                    }
                }
            }

            // Supprimer l'appartenance de l'utilisateur à toutes les équipes
            DB::table('team_user')->where('user_id', $user->id)->delete();

            // Supprimer les invitations d'équipe en attente
            DB::table('team_invitations')->where('email', $user->email)->delete();

            // Supprimer les tâches assignées à cet utilisateur (ou les réassigner à null)
            Task::where('assigned_to', $user->id)->update(['assigned_to' => null]);

            // Marquer les tâches créées par cet utilisateur (si nécessaire)
            Task::where('created_by', $user->id)->update(['created_by' => auth()->id()]);

            // Transférer ou supprimer les projets créés par l'utilisateur
            Project::where('created_by', $user->id)->update(['created_by' => auth()->id()]);

            // Objectifs créés par l'utilisateur
            Objective::where('created_by', $user->id)->update(['created_by' => auth()->id()]);

            // Supprimer les sessions de l'utilisateur
            DB::table('sessions')->where('user_id', $user->id)->delete();

            // Supprimer les tokens d'API
            $user->tokens()->delete();

            // Finalement, supprimer l'utilisateur
            $user->delete();

            DB::commit();

            return redirect()->route('admin.users.list')->with('success', 'Utilisateur supprimé avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Une erreur est survenue lors de la suppression de l\'utilisateur : ' . $e->getMessage());
        }
    }

    /************************************
     * GESTION DES ÉQUIPES
     ************************************/

    /**
     * Liste toutes les équipes
     */
    public function teamsList(Request $request)
    {
        $query = Team::query();

        // Recherche par nom
        if ($request->has('search') && !empty($request->search)) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Pagination
        $teams = $query->with('owner')
            ->withCount('users')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/TeamsList', [
            'teams' => $teams,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Afficher le formulaire de création d'une équipe
     */
    public function createTeam()
    {
        // Récupérer tous les utilisateurs qui pourraient être propriétaires de l'équipe
        $users = User::orderBy('name')
            ->get(['id', 'name', 'email', 'role']);

        return Inertia::render('Admin/CreateTeam', [
            'users' => $users,
        ]);
    }

    /**
     * Enregistrer une nouvelle équipe
     */
    public function storeTeam(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'owner_id' => 'required|exists:users,id',
            'personal_team' => 'boolean',
        ]);

        $owner = User::findOrFail($validated['owner_id']);

        // Créer l'équipe sans utiliser le mass assignment
        $team = new Team();
        $team->name = $validated['name'];
        $team->user_id = $validated['owner_id'];
        $team->personal_team = true;
        $team->save();

        // Si l'équipe n'est pas personnelle, ajouter le propriétaire comme membre
        if (!$team->personal_team) {
            $team->users()->attach($owner, ['role' => $owner->role]);
        }

        return redirect()->to('/admin/teams')->with('success', 'Équipe créée avec succès');
    }

    /**
     * Affiche les détails d'une équipe
     */
    public function teamDetails(Team $team)
    {
        $team->load('owner');
        $teamMembers = $team->users()->get()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'team_role' => $user->pivot ? $user->pivot->role : 'N/A',
            ];
        });

        $projects = Project::where('team_id', $team->id)
            ->withCount('tasks')
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Admin/TeamDetails', [
            'team' => $team,
            'members' => $teamMembers,
            'projects' => $projects,
        ]);
    }

    /**
     * Supprimer une équipe
     */
    public function destroyTeam(Team $team)
    {
        // Vérifier que l'équipe n'est pas une équipe personnelle
        if ($team->personal_team) {
            return redirect()->back()->with('error', 'Vous ne pouvez pas supprimer une équipe personnelle');
        }

        // Vérifier que l'utilisateur actuel est administrateur ou propriétaire de l'équipe
        $user = auth()->user();
        if ($user->role !== 'administrateur' && $team->user_id !== $user->id) {
            return redirect()->back()->with('error', 'Vous n\'avez pas les droits pour supprimer cette équipe');
        }

        // Détacher tous les membres de l'équipe
        DB::table('team_user')
            ->where('team_id', $team->id)
            ->delete();

        // Supprimer les invitations en attente
        DB::table('team_invitations')
            ->where('team_id', $team->id)
            ->delete();

        // Si vous avez des tâches liées aux équipes, vous devrez aussi les supprimer
        // Task::where('team_id', $team->id)->delete();

        // Si vous avez des projets liés aux équipes, vous devrez aussi les supprimer
        // Project::where('team_id', $team->id)->delete();

        // Supprimer l'équipe
        $team->delete();

        return redirect()->route('admin.teams.list')->with('success', 'Équipe supprimée avec succès');
    }

        /**
     * Afficher le formulaire d'ajout d'un membre à une équipe
     */
    public function createTeamMember(Team $team)
    {
        // Récupérer les utilisateurs qui ne sont pas déjà membres de l'équipe
        $users = User::whereDoesntHave('teams', function($query) use ($team) {
                $query->where('teams.id', $team->id);
            })
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'role']);

        return Inertia::render('Admin/CreateTeamMember', [
            'team' => [
                'id' => $team->id,
                'name' => $team->name,
            ],
            'availableUsers' => $users,
        ]);
    }

    /**
     * Ajouter un membre à une équipe
     */
    public function storeTeamMember(Request $request, Team $team)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:administrateur,manager,collaborateur',
        ]);

        $user = User::findOrFail($validated['user_id']);

        // Vérifier si l'utilisateur n'est pas déjà membre de l'équipe
        if (!$team->users()->where('user_id', $user->id)->exists()) {
            $team->users()->attach($user, ['role' => $validated['role']]);
            return redirect()->route('admin.teams.show', $team->id)->with('success', 'Membre ajouté avec succès');
        }

        return redirect()->back()->with('error', 'Cet utilisateur est déjà membre de l\'équipe');
    }

    /**
     * Afficher le formulaire d'édition d'un membre d'équipe
     */
    public function editTeamMember(Team $team, User $user)
    {
        // Vérifier que l'utilisateur est bien membre de l'équipe
        if (!$user->belongsToTeam($team)) {
            return redirect()->route('admin.teams.show', $team->id)
                ->with('error', 'Cet utilisateur n\'est pas membre de l\'équipe');
        }

        // Récupérer le rôle actuel du membre dans l'équipe
        $membership = DB::table('team_user')
            ->where('team_id', $team->id)
            ->where('user_id', $user->id)
            ->first();

        return Inertia::render('Admin/EditTeamMember', [
            'team' => [
                'id' => $team->id,
                'name' => $team->name,
            ],
            'member' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'team_role' => $membership ? $membership->role : 'collaborateur',
            ],
        ]);
    }

    /**
     * Mettre à jour le rôle d'un membre dans une équipe
     */
    public function updateTeamMember(Request $request, Team $team, User $user)
    {
        $validated = $request->validate([
            'role' => 'required|in:administrateur,manager,collaborateur',
        ]);

        // Vérifier que l'utilisateur est bien membre de l'équipe
        if (!$user->belongsToTeam($team)) {
            return redirect()->route('admin.teams.show', $team->id)
                ->with('error', 'Cet utilisateur n\'est pas membre de l\'équipe');
        }

        // Mettre à jour le rôle dans la table pivot
        $team->users()->updateExistingPivot($user->id, ['role' => $validated['role']]);

        return redirect()->route('admin.teams.show', $team->id)
            ->with('success', 'Rôle du membre mis à jour avec succès');
    }

    /**
     * Supprimer un membre d'une équipe
     */
    public function destroyTeamMember(Team $team, User $user)
    {
        // Vérifier que l'équipe n'est pas personnelle
        if ($team->personal_team) {
            return redirect()->back()->with('error', 'Vous ne pouvez pas retirer des membres d\'une équipe personnelle');
        }

        // Vérifier que l'utilisateur n'est pas le propriétaire de l'équipe
        if ($team->user_id === $user->id) {
            return redirect()->back()->with('error', 'Vous ne pouvez pas retirer le propriétaire de l\'équipe');
        }

        // Vérifier que l'utilisateur est bien membre de l'équipe
        if (!$user->belongsToTeam($team)) {
            return redirect()->back()->with('error', 'Cet utilisateur n\'est pas membre de l\'équipe');
        }

        // Retirer le membre de l'équipe
        $team->removeUser($user);

        return redirect()->route('admin.teams.show', $team->id)
            ->with('success', 'Membre retiré de l\'équipe avec succès');
    }

/**
 * Afficher le formulaire d'édition d'une équipe
 */
public function editTeam(Team $team)
{
    // Récupérer tous les utilisateurs qui pourraient être propriétaires de l'équipe
    $users = User::orderBy('name')
        ->get(['id', 'name', 'email', 'role']);

    return Inertia::render('Admin/EditTeam', [
        'team' => [
            'id' => $team->id,
            'name' => $team->name,
            'personal_team' => $team->personal_team,
            'user_id' => $team->user_id,
        ],
        'users' => $users,
    ]);
}

    /**
     * Mettre à jour une équipe
     */
    public function updateTeam(Request $request, Team $team)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'owner_id' => 'required|exists:users,id',
        ]);

        // Vérifier si l'équipe est personnelle
        if ($team->personal_team) {
            return redirect()->back()->with('error', 'Vous ne pouvez pas modifier le propriétaire d\'une équipe personnelle.');
        }

        // Mettre à jour le nom de l'équipe
        $team->name = $validated['name'];

        // Si le propriétaire change, mettre à jour
        if ($team->user_id != $validated['owner_id']) {
            $newOwner = User::findOrFail($validated['owner_id']);

            // S'assurer que le nouveau propriétaire est membre de l'équipe
            if (!$team->users()->where('user_id', $newOwner->id)->exists()) {
                $team->users()->attach($newOwner, ['role' => $newOwner->role]);
            }

            $team->user_id = $validated['owner_id'];
        }

        $team->save();

        return redirect()->route('admin.teams.show', $team->id)->with('success', 'Équipe mise à jour avec succès');
    }

    /**
     * Afficher le formulaire de création d'un projet
     */
    public function createProject(Request $request)
    {
        // Récupérer l'ID de l'équipe s'il est fourni dans la requête
        $team_id = $request->input('team_id');

        // Récupérer toutes les équipes pour le sélecteur
        $teams = Team::orderBy('name')->get(['id', 'name']);

        // Si team_id est spécifié, récupérer et vérifier cette équipe
        $selectedTeam = null;
        if ($team_id) {
            $selectedTeam = Team::findOrFail($team_id);
        }

        // Récupérer les utilisateurs qui pourraient être assignés au projet
        $users = User::orderBy('name')
            ->get(['id', 'name', 'email', 'role']);

        return Inertia::render('Admin/CreateProject', [
            'teams' => $teams,
            'selectedTeamId' => $team_id,
            'users' => $users,
        ]);
    }

    /**
     * Enregistrer un nouveau projet
     */
    public function storeProject(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'team_id' => 'required|exists:teams,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:active,on_hold,completed',
            'priority' => 'required|in:low,medium,high',
            'assigned_users' => 'nullable|array',
            'assigned_users.*' => 'exists:users,id',
        ]);

        // Créer le projet
        $project = new Project();
        $project->name = $validated['name'];
        $project->description = $validated['description'] ?? null;
        $project->team_id = $validated['team_id'];
        $project->start_date = $validated['start_date'] ?? null;
        $project->end_date = $validated['end_date'] ?? null;
        $project->status = $validated['status'];
        $project->priority = $validated['priority'];
        $project->created_by = auth()->id();
        $project->save();

        // Associer les utilisateurs assignés si spécifiés
        if (isset($validated['assigned_users']) && is_array($validated['assigned_users'])) {
            // Note: Assurez-vous que la relation existe dans le modèle Project
            // Si elle n'existe pas, vous devrez créer une nouvelle table pivot
            // $project->assignedUsers()->attach($validated['assigned_users']);
        }

        // Rediriger vers la liste des projets ou les détails du projet créé
        return redirect()->route('admin.projects.show', $project->id)
            ->with('success', 'Projet créé avec succès');
    }

    /************************************
     * GESTION SYSTÈME
     ************************************/

    /**
     * Afficher la page des paramètres système
     */
    public function systemLogs()
    {
        $systemInfo = [
            'laravelVersion' => app()->version(),
            'phpVersion' => PHP_VERSION,
            'environment' => config('app.env'),
            'debug' => config('app.debug'),
            'database' => config('database.default'),
        ];

        $maintenanceFile = storage_path('framework/down');
        $maintenance = [
            'enabled' => file_exists($maintenanceFile),
            'since' => file_exists($maintenanceFile)
                ? Carbon::createFromTimestamp(filectime($maintenanceFile))->diffForHumans()
                : null
        ];

        return Inertia::render('Admin/SystemLogs', [
            'systemInfo' => $systemInfo,
            'maintenance' => $maintenance,
        ]);
    }
}
