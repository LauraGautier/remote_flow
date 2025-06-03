<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'team_id',
        'login_time',
        'logout_time'
    ];

    protected $casts = [
        'login_time' => 'datetime',
        'logout_time' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    // Durée de la session en minutes
    public function getDurationInMinutesAttribute()
    {
        if ($this->login_time && $this->logout_time) {
            return $this->login_time->diffInMinutes($this->logout_time);
        }

        // Si l'utilisateur est toujours connecté, calculer la durée jusqu'à maintenant
        if ($this->login_time && !$this->logout_time) {
            return $this->login_time->diffInMinutes(now());
        }

        return 0;
    }
}
