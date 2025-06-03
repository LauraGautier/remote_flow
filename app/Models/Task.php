<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'team_id',
        'project_id',
        'assigned_to',
        'status',
        'start_time',
        'end_time',
        'created_by'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    // Relations
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Helpers
    public function getDurationAttribute()
    {
        if ($this->start_time && $this->end_time) {
            return $this->end_time->diffInMinutes($this->start_time);
        }
        return null;
    }

    public function isStarted()
    {
        return $this->start_time !== null && $this->end_time === null;
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
