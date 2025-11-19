<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectTask extends Model
{
    protected $fillable = [
        'project_column_id',
        'title',
        'description',
        'order',
        'assigned_to',
        'due_date',
        'due_date_jalali',
        'priority',
        'is_completed',
        'completed_at',
        'completed_by',
    ];

    protected $casts = [
        'due_date' => 'date',
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function column()
    {
        return $this->belongsTo(ProjectColumn::class, 'project_column_id');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the users assigned to this task (many-to-many).
     */
    public function assignedUsers()
    {
        return $this->belongsToMany(User::class, 'project_task_user')
            ->withTimestamps();
    }

    /**
     * Get the user who completed the task.
     */
    public function completedBy()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }
}
