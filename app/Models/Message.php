<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'recipient_id',
        'conversation_id',
        'team_id',
        'content',
        'subject',
        'is_read',
        'read_at'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    // Relations
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    // Méthodes utiles
    public function markAsRead()
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now()
            ]);
        }
    }

    public function isFromCurrentUser()
    {
        return $this->sender_id === auth()->id();
    }

    // Scope pour les messages non lus
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    // Scope pour les messages reçus par un utilisateur
    public function scopeReceivedBy($query, $userId)
    {
        return $query->where('recipient_id', $userId);
    }

    // Scope pour les messages envoyés par un utilisateur
    public function scopeSentBy($query, $userId)
    {
        return $query->where('sender_id', $userId);
    }
}
