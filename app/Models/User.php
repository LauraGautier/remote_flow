<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use HasTeams;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'user_id',
        'personal_team',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get all of the sessions for the user.
     */
    public function sessions()
    {
        return $this->hasMany(UserSession::class);
    }

    /**
     * Get the teams the user owns.
     */
    public function ownedTeams()
    {
        return $this->hasMany(Team::class);
    }

    /**
     * Get all of the teams the user belongs to.
     */
    public function teams()
    {
        return $this->belongsToMany(Team::class, 'team_user')
                    ->withPivot(['role'])
                    ->withTimestamps()
                    ->as('membership');
    }

    /**
     * Get all of the tasks assigned to the user.
     */
    public function tasks()
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    /**
     * Get all of the tasks created by the user.
     */
    public function createdTasks()
    {
        return $this->hasMany(Task::class, 'created_by');
    }

    /**
     * Get all of the projects created by the user.
     */
    public function createdProjects()
    {
        return $this->hasMany(Project::class, 'created_by');
    }

    /**
     * Get all of the objectives created by the user.
     */
    public function createdObjectives()
    {
        return $this->hasMany(Objective::class, 'created_by');
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'recipient_id');
    }

    public function conversations()
    {
        return Conversation::where('user_one', $this->id)
            ->orWhere('user_two', $this->id);
    }

    // Méthodes utiles pour la messagerie
    public function getUnreadMessagesCount()
    {
        return $this->receivedMessages()
            ->where('is_read', false)
            ->count();
    }

    public function getConversationWith(User $user)
    {
        return Conversation::between($this->id, $user->id);
    }

    public function hasUnreadMessageFrom(User $user)
    {
        return $this->receivedMessages()
            ->where('sender_id', $user->id)
            ->where('is_read', false)
            ->exists();
    }

    // Messages récents pour la boîte de réception
    public function recentMessages()
    {
        return $this->receivedMessages()
            ->with(['sender', 'conversation'])
            ->orderBy('created_at', 'desc')
            ->limit(10);
    }
}
