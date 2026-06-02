<?php

namespace App\Http\Controllers\Api\Task;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\TaskRequest;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\SuccessResource;
use App\Http\Resources\Task\TaskResource;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projectId = request()->query('project_id');
        $query = Task::with('project', 'assigned', 'comments');

        if ($projectId) {
            $query->where('project_id', $projectId);
        }

        $tasks = $query->paginate(20);

        return new SuccessResource([
            'message' => 'Tasks retrieved successfully',
            'data' => TaskResource::collection($tasks),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TaskRequest $request)
    {
        $validated = $request->validated();
        
        if (!empty($validated['due_date'])) {
            $validated['due_date'] = Carbon::createFromFormat('d-m-Y', $validated['due_date'])->format('Y-m-d');
        }

        if (!empty($validated['completed_at'])) {
            $validated['completed_at'] = Carbon::createFromFormat('d-m-Y', $validated['completed_at'])->format('Y-m-d');
        }

        try {
            $task = Task::create($validated);

            $task->load('project', 'assigned', 'comments');

            return new SuccessResource([
                'message' => 'Task created successfully',
                'data' => new TaskResource($task),
                'status_code' => 201,
            ]);
        } catch (\Exception $e) {
            return new ErrorResource([
                'message' => 'Failed to create task',
                'errors' => $e->getMessage(),
                'status_code' => 500,
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $task = Task::with('project', 'assigned', 'comments')->find($id);

        if (!$task) {
            return new ErrorResource([
                'message' => 'Task not found',
                'status_code' => 404,
            ]);
        }

        return new SuccessResource([
            'message' => 'Task retrieved successfully',
            'data' => new TaskResource($task),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TaskRequest $request, string $id)
    {
        $task = Task::find($id);

        if (!$task) {
            return new ErrorResource([
                'message' => 'Task not found',
                'status_code' => 404,
            ]);
        }

        $validated = $request->validated();

        try {
            $task->update($validated);

            $task->fresh()->load('project', 'assigned', 'comments');

            return new SuccessResource([
                'message' => 'Task updated successfully',
                'data' => new TaskResource($task),
            ]);
        } catch (\Exception $e) {
            return new ErrorResource([
                'message' => 'Failed to update task',
                'errors' => $e->getMessage(),
                'status_code' => 500,
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $task = Task::find($id);

        if (!$task) {
            return new ErrorResource([
                'message' => 'Task not found',
                'status_code' => 404,
            ]);
        }

        try {
            $task->delete();

            return new SuccessResource([
                'message' => 'Task deleted successfully',
            ]);
        } catch (\Exception $e) {
            return new ErrorResource([
                'message' => 'Failed to delete task',
                'errors' => $e->getMessage(),
                'status_code' => 500,
            ]);
        }
    }
}
