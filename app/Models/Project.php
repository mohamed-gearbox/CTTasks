<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'active',
    ];

    /**
     * project tasks
     * @return HasMany
     */
    public function tasks(): HasMany
    {
        return $this->hasMany('App\Models\Task');
    }

    /**
     * get the lowest task priority
     * @return int
     */
    public function getMaxTaskPriority(): int
    {
        return $this->tasks()->max('priority') ?? 1;
    }
}
