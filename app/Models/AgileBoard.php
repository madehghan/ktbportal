<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgileBoard extends Model
{
    use HasFactory;

    protected $table = 'agile_board';

    protected $fillable = [
        'content',
    ];

    /**
     * Get or create the single agile board instance.
     */
    public static function getInstance()
    {
        return static::firstOrCreate(['id' => 1], ['content' => '']);
    }
}
