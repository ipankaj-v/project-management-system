<?php

namespace App\Http\Resources\Task;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'project' => $this->whenLoaded('project', function () {
                return ['id' => $this->project?->id, 'name' => $this->project?->name];
            }),
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'priority' => $this->priority,
            'assigned_to' => $this->whenLoaded('assigned', function () {
                return ['id' => $this->assigned?->id, 'name' => $this->assigned?->name];
            }),
            'due_date' => $this->due_date,
            'completed_at' => $this->completed_at,
            'comments_count' => $this->when(isset($this->comments), fn() => $this->comments->count()),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
