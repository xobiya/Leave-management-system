<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory, \App\Traits\LogsActivity;

    protected $fillable = ['name', 'code', 'manager_id', 'status'];

    protected $casts = [
        'status' => 'string',
    ];

    public function milestones(): HasMany
    {
        return $this->hasMany(Milestone::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
}
