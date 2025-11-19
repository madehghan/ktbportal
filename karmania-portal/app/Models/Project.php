<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'start_date',
        'start_date_jalali',
        'end_date',
        'end_date_jalali',
        'status',
        'budget',
        'notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'budget' => 'decimal:2',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the users (employees) assigned to this project.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'project_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Get the customers associated with this project.
     */
    public function customers()
    {
        return $this->belongsToMany(Customer::class, 'customer_project')
            ->withTimestamps();
    }

    /**
     * Get the tasks for this project.
     */
    public function tasks()
    {
        return $this->hasMany(ProjectTask::class)->orderBy('order');
    }
}
