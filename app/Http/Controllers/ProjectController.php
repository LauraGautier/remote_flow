<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $team = $user->currentTeam;

        $projects = Project::where('team_id', $team->id)
            ->with(['creator:id,name', 'tasks', 'objectives'])
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Projects/Index', [
            'projects' => $projects,
            'team' => $team
        ]);
    }

    public function create()
    {
        $team = auth()->user()->currentTeam;

        return Inertia::render('Projects/Create', [
            'team' => $team,
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $team = $user->currentTeam;

        $this->authorize('create', Project::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $project = Project::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'team_id' => $team->id,
            'created_by' => $user->id,
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'status' => 'active',
        ]);

        return redirect()->route('projects.show', $project)->with('success', 'Projet créé avec succès');
    }

    public function show(Project $project)
    {
        $this->authorize('view', $project);

        $project->load(['creator:id,name']);

        $tasks = Task::where('project_id', $project->id)
            ->with(['assignedTo:id,name,email', 'creator:id,name'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Chargement des objectifs
        $objectives = $project->objectives()
            ->orderBy('created_at', 'desc')
            ->get();

        // Calcul des KPIs
        $totalTimeSpent = $project->getFormattedTimeSpentAttribute();
        $objectivesCompletionPercentage = $project->getObjectivesCompletionPercentageAttribute();

        // Statistiques sur les tâches
        $taskStats = [
            'total' => $tasks->count(),
            'pending' => $tasks->where('status', 'pending')->count(),
            'in_progress' => $tasks->where('status', 'in_progress')->count(),
            'completed' => $tasks->where('status', 'completed')->count(),
        ];

        return Inertia::render('Projects/Show', [
            'project' => $project,
            'tasks' => $tasks,
            'objectives' => $objectives,
            'taskStats' => $taskStats,
            'kpis' => [
                'totalTimeSpent' => $totalTimeSpent,
                'objectivesCompletionPercentage' => $objectivesCompletionPercentage
            ],
            'canEdit' => auth()->user()->role === 'manager',
        ]);
    }

    public function edit(Project $project)
    {
        $this->authorize('update', $project);

        return Inertia::render('Projects/Edit', [
            'project' => $project,
        ]);
    }

    public function update(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:active,completed,on_hold',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $project->update($validated);

        return redirect()->route('projects.show', $project)->with('success', 'Projet mis à jour avec succès');
    }

    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);

        $project->delete();

        return redirect()->route('projects.index')->with('success', 'Projet supprimé avec succès');
    }
}
