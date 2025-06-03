<?php

namespace App\Http\Controllers;

use App\Models\Objective;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ObjectiveController extends Controller
{
    /**
     * Créer un nouvel objectif pour un projet
     */
    public function store(Request $request, Project $project)
    {
        Gate::authorize('update', $project);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
        ]);

        $objective = Objective::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'project_id' => $project->id,
            'created_by' => auth()->id(),
            'is_completed' => false,
            'due_date' => $validated['due_date'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Objectif créé avec succès');
    }

    /**
     * Mettre à jour un objectif
     */
    public function update(Request $request, Objective $objective)
    {
        Gate::authorize('update', $objective->project);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_completed' => 'boolean',
            'due_date' => 'nullable|date',
        ]);

        // Si l'objectif est marqué comme complété, enregistrer la date
        if (isset($validated['is_completed']) && $validated['is_completed'] && !$objective->is_completed) {
            $validated['completed_at'] = now();
        } elseif (isset($validated['is_completed']) && !$validated['is_completed']) {
            $validated['completed_at'] = null;
        }

        $objective->update($validated);

        return redirect()->back()->with('success', 'Objectif mis à jour avec succès');
    }

    /**
     * Supprimer un objectif
     */
    public function destroy(Objective $objective)
    {
        Gate::authorize('update', $objective->project);

        $objective->delete();

        return redirect()->back()->with('success', 'Objectif supprimé avec succès');
    }

    /**
     * Basculer l'état de complétion d'un objectif
     */
    public function toggleCompletion(Objective $objective)
    {
        Gate::authorize('update', $objective->project);

        $isCompleted = !$objective->is_completed;

        $objective->update([
            'is_completed' => $isCompleted,
            'completed_at' => $isCompleted ? now() : null,
        ]);

        return redirect()->back()->with('success',
            'Objectif marqué comme ' . ($isCompleted ? 'atteint' : 'non atteint'));
    }
}
