<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Project extends Model
{
    /**
     * Mass assignable attributes.
     *
     * @var string[]
     */
    protected $fillable = [
        'organization_id',
        'name',
        'description',
        'owner_id',
        'status_id',
        'start_date',
        'end_date',
    ];

    /**
     * Organization that owns the project.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Owner (user) of the project.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Status of the project.
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(ProjectStatus::class, 'status_id');
    }

    /**
     * Tasks under this project.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Users assigned / members of the project.
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_user')
                    ->withTimestamps()
                    ->withPivot('role');
    }

    /**
     * Polymorphic attachments for the project.
     */
    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    /**
     * Activity log entries related to this project.
     */
    public function activities(): MorphMany
    {
        return $this->morphMany(Activity::class, 'subject');
    }
}
