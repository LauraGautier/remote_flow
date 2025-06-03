<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_one',
        'user_two',
        'last_message_at'
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    // Relations
    public function userOne(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_one');
    }

    public function userTwo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_two');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'desc');
    }

    // Méthodes utiles
    public function getOtherUser($userId)
    {
        return $this->user_one === $userId ? $this->userTwo : $this->userOne;
    }

    public function hasUser($userId)
    {
        return $this->user_one === $userId || $this->user_two === $userId;
    }

    public function getLastMessage()
    {
        return $this->messages()->latest()->first();
    }

    public function getUnreadCount($userId)
    {
        return $this->messages()
            ->where('recipient_id', $userId)
            ->where('is_read', false)
            ->count();
    }

    // Méthode statique pour trouver ou créer une conversation entre deux utilisateurs
    public static function between($userOne, $userTwo)
    {
        // S'assurer que user_one est toujours le plus petit ID (pour la cohérence)
        $user1 = min($userOne, $userTwo);
        $user2 = max($userOne, $userTwo);

        return self::firstOrCreate([
            'user_one' => $user1,
            'user_two' => $user2
        ]);
    }
}
