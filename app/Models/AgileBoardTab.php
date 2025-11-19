<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AgileBoardTab extends Model
{
    protected $fillable = [
        'name',
        'content',
        'order',
        'created_by',
    ];

    /**
     * Get the user who created this tab.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the users who have access to this tab.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'agile_board_tab_user')
            ->withTimestamps();
    }
}
