<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'url',
        'username',
        'password',
        'description',
    ];

    /**
     * Get the project that owns the account.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
