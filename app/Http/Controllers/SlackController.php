<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class SlackController extends Controller
{
    /**
     * Afficher la page Slack
     */
    public function index()
    {
        $user = auth()->user();
        $team = $user->currentTeam;

        // Canal par défaut
        $defaultChannel = config('services.slack.default_channel', '#general');

        // 🔥 SOLUTION DIRECTE - On ignore complètement les variables ENV
        $webhookUrl = 'https://hooks.slack.com/services/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';
        $hasWebhookUrl = !empty($webhookUrl);

        return Inertia::render('Slack/Index', [
            'defaultChannel' => '#general',
            'team' => $team->only('id', 'name'),
            'user' => $user->only('id', 'name', 'email'),
            'hasWebhookUrl' => $hasWebhookUrl, // Sera toujours true maintenant
        ]);
    }

    /**
     * Envoyer un message sur Slack
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'channel' => 'nullable|string',
        ]);

        $webhookUrl = 'https://hooks.slack.com/services/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';

        if (!$webhookUrl) {
            return response()->json([
                'success' => false,
                'message' => 'Webhook Slack non configuré. Ajoutez SLACK_WEBHOOK_URL dans votre .env'
            ], 400);
        }

        // SÉCURITÉ : Toujours utiliser l'utilisateur connecté, jamais les données du front
        $user = auth()->user();
        $channel = $request->channel ?: config('services.slack.default_channel', '#general');

        try {
            $response = Http::post($webhookUrl, [
                'channel' => $channel,
                'username' => 'TaskApp',
                'text' => $request->message . " - " . $user->name, // Message + nom collé
                'icon_emoji' => ':speech_balloon:',
            ]);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Message envoyé sur Slack avec succès !'
                ]);
            } else {
                Log::error('Erreur Slack: ' . $response->body());
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de l\'envoi sur Slack'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Exception Slack: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur de connexion à Slack: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Partager les KPIs sur Slack
     */
    public function shareKpis(Request $request)
    {
        $webhookUrl = 'https://hooks.slack.com/services/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';

        if (!$webhookUrl) {
            return response()->json([
                'success' => false,
                'message' => 'Webhook Slack non configuré'
            ], 400);
        }

        $user = auth()->user();
        $team = $user->currentTeam;

        // Récupération des KPIs basiques
        $teamIds = $user->allTeams()->pluck('id')->toArray();

        $totalTasks = \App\Models\Task::whereIn('team_id', $teamIds)
            ->where('assigned_to', $user->id)
            ->count();

        $completedTasks = \App\Models\Task::whereIn('team_id', $teamIds)
            ->where('assigned_to', $user->id)
            ->where('status', 'completed')
            ->count();

        $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

        $message = "📊 *KPIs de {$user->name}*\n\n" .
                  "✅ Tâches terminées: {$completedTasks}\n" .
                  "📋 Total des tâches: {$totalTasks}\n" .
                  "📈 Taux de réussite: {$completionRate}%\n\n" .
                  "_Généré depuis TaskApp_";

        try {
            $response = Http::post($webhookUrl, [
                'channel' => config('services.slack.default_channel', '#general'),
                'username' => 'TaskApp KPI Bot',
                'text' => $message,
                'icon_emoji' => ':bar_chart:',
            ]);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'KPIs partagés sur Slack avec succès !'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors du partage des KPIs'
                ], 500);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de connexion à Slack'
            ], 500);
        }
    }

    /**
     * Envoyer une notification automatique lors de l'assignation d'une tâche
     */
    public static function notifyTaskAssigned($task)
    {
        $webhookUrl = 'https://hooks.slack.com/services/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';

        if (!$webhookUrl || !$task->assignedTo) {
            return false;
        }

        $message = "🎯 *Nouvelle tâche assignée*\n\n" .
                  "*{$task->title}*\n" .
                  "Assignée à: {$task->assignedTo->name}\n" .
                  "Projet: {$task->project->name}\n" .
                  "Créée par: {$task->creator->name}";

        try {
            Http::post($webhookUrl, [
                'channel' => config('services.slack.default_channel', '#general'),
                'username' => 'TaskApp', // Bot pour les notifications auto
                'text' => $message,
                'icon_emoji' => ':dart:',
            ]);
            return true;
        } catch (\Exception $e) {
            Log::error('Erreur notification Slack: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Envoyer une notification lors de la completion d'une tâche
     */
    public static function notifyTaskCompleted($task)
    {
        $webhookUrl = 'https://hooks.slack.com/services/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';

        if (!$webhookUrl) {
            return false;
        }

        $duration = $task->getDurationAttribute();
        $durationText = '';
        if ($duration) {
            $hours = floor($duration / 60);
            $minutes = $duration % 60;
            $durationText = "\n⏱️ Durée: {$hours}h {$minutes}m";
        }

        $message = "✅ *Tâche terminée*\n\n" .
                  "*{$task->title}*\n" .
                  "Terminée par: {$task->assignedTo->name}\n" .
                  "Projet: {$task->project->name}" .
                  $durationText;

        try {
            Http::post($webhookUrl, [
                'channel' => config('services.slack.default_channel', '#general'),
                'username' => 'TaskApp',
                'text' => $message,
                'icon_emoji' => ':white_check_mark:',
            ]);
            return true;
        } catch (\Exception $e) {
            Log::error('Erreur notification Slack: ' . $e->getMessage());
            return false;
        }
    }
}
