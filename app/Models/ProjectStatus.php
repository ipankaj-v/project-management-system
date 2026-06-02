<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectStatus extends Model
{
    /**
     * Mass assignable attributes.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'color',
    ];

    /**
     * Projects with this status.
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'status_id');
    }
}
