<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectColumn extends Model
{
    protected $fillable = [
        'project_id',
        'name',
        'color',
        'order',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function tasks()
    {
        return $this->hasMany(ProjectTask::class)->orderBy('order');
    }
}
