<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Message;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MessageController extends Controller
{
    // Liste des conversations
    public function index()
    {
        $conversations = auth()->user()->conversations()
            ->with(['userOne', 'userTwo', 'messages' => function($query) {
                $query->latest()->limit(1);
            }])
            ->get()
            ->map(function($conversation) {
                $otherUser = $conversation->getOtherUser(auth()->id());
                $lastMessage = $conversation->getLastMessage();

                return [
                    'id' => $conversation->id,
                    'other_user' => $otherUser,
                    'last_message' => $lastMessage,
                    'unread_count' => $conversation->getUnreadCount(auth()->id()),
                    'last_message_at' => $conversation->last_message_at
                ];
            })
            ->sortByDesc('last_message_at')
            ->values();

        return Inertia::render('Messages/Index', [
            'conversations' => $conversations,
            'unreadCount' => auth()->user()->getUnreadMessagesCount()
        ]);
    }

    // Afficher une conversation
    public function show(Conversation $conversation)
    {
        // Vérifier que l'utilisateur fait partie de la conversation
        if (!$conversation->hasUser(auth()->id())) {
            abort(403);
        }

        // Marquer les messages comme lus
        $conversation->messages()
            ->where('recipient_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        $messages = $conversation->messages()
            ->with(['sender', 'recipient'])
            ->orderBy('created_at', 'asc')
            ->get();

        $otherUser = $conversation->getOtherUser(auth()->id());

        return Inertia::render('Messages/Show', [
            'conversation' => $conversation,
            'messages' => $messages,
            'otherUser' => $otherUser
        ]);
    }

    // Formulaire pour nouveau message
    public function create(User $user = null)
    {
        // Option 1 : Récupérer tous les utilisateurs sauf l'utilisateur connecté
        $users = User::where('id', '!=', auth()->id())
            ->orderBy('name')
            ->get();

        // Option 2 : Si tu veux limiter aux utilisateurs de la même équipe
        // $users = User::where('id', '!=', auth()->id())
        //     ->whereHas('teams', function($query) {
        //         $teamIds = auth()->user()->teams->pluck('id');
        //         $query->whereIn('teams.id', $teamIds);
        //     })
        //     ->orderBy('name')
        //     ->get();

        // Option 3 : Si tu veux limiter aux utilisateurs de l'équipe courante
        // $currentTeamId = auth()->user()->current_team_id;
        // $users = User::where('id', '!=', auth()->id())
        //     ->whereHas('teams', function($query) use ($currentTeamId) {
        //         $query->where('teams.id', $currentTeamId);
        //     })
        //     ->orderBy('name')
        //     ->get();

        return Inertia::render('Messages/Create', [
            'users' => $users,
            'selectedUser' => $user
        ]);
    }

    // Envoyer un message
    public function store(Request $request)
    {
        $validated = $request->validate([
            'recipient_id' => 'required|exists:users,id|different:' . auth()->id(),
            'content' => 'required|string|max:5000',
            'subject' => 'nullable|string|max:255'
        ]);

        // Créer ou récupérer la conversation
        $conversation = Conversation::between(auth()->id(), $validated['recipient_id']);

        // Créer le message
        $message = Message::create([
            'sender_id' => auth()->id(),
            'recipient_id' => $validated['recipient_id'],
            'conversation_id' => $conversation->id,
            'team_id' => auth()->user()->currentTeam->id ?? null,
            'content' => $validated['content'],
            'subject' => $validated['subject']
        ]);

        // Mettre à jour la conversation
        $conversation->update([
            'last_message_at' => now()
        ]);

        return redirect()->route('messages.show', $conversation);
    }

    // Répondre à un message
    public function reply(Conversation $conversation, Request $request)
    {
        // Vérifier que l'utilisateur fait partie de la conversation
        if (!$conversation->hasUser(auth()->id())) {
            abort(403);
        }

        $validated = $request->validate([
            'content' => 'required|string|max:5000'
        ]);

        $otherUserId = $conversation->getOtherUser(auth()->id())->id;

        $message = Message::create([
            'sender_id' => auth()->id(),
            'recipient_id' => $otherUserId,
            'conversation_id' => $conversation->id,
            'team_id' => auth()->user()->currentTeam->id ?? null,
            'content' => $validated['content']
        ]);

        $conversation->update([
            'last_message_at' => now()
        ]);

        return back();
    }

    // Supprimer un message
    public function destroy(Message $message)
    {
        if ($message->sender_id !== auth()->id()) {
            abort(403);
        }

        $message->delete();

        return back();
    }

    // Marquer un message comme lu
    public function markAsRead(Message $message)
    {
        if ($message->recipient_id !== auth()->id()) {
            abort(403);
        }

        $message->markAsRead();

        return response()->json(['success' => true]);
    }

    // Obtenir le nombre de messages non lus
    public function unreadCount()
    {
        return response()->json([
            'count' => auth()->user()->getUnreadMessagesCount()
        ]);
    }
}
