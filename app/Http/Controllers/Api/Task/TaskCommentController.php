<?php

namespace App\Http\Controllers\Api\Task;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\TaskCommentRequest;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\SuccessResource;
use App\Http\Resources\Task\TaskResource;
use App\Models\Task;
use App\Models\TaskComment;

class TaskCommentController extends Controller
{
    public function store(TaskCommentRequest $request)
    {
        $validated = $request->validated();

        try {
            $comment = TaskComment::create($validated + ['user_id' => $request->user()?->id]);

            $task = Task::with('project', 'assigned', 'comments')->find($validated['task_id']);

            return new SuccessResource([
                'message' => 'Comment added',
                'data' => new TaskResource($task),
                'status_code' => 201,
            ]);
        } catch (\Exception $e) {
            return new ErrorResource([
                'message' => 'Failed to add comment',
                'errors' => $e->getMessage(),
                'status_code' => 500,
            ]);
        }
    }

    public function destroy($id)
    {
        $comment = TaskComment::find($id);

        if (!$comment) {
            return new ErrorResource([
                'message' => 'Comment not found',
                'status_code' => 404,
            ]);
        }

        try {
            $taskId = $comment->task_id;
            $comment->delete();

            $task = Task::with('project', 'assigned', 'comments')->find($taskId);

            return new SuccessResource([
                'message' => 'Comment deleted',
                'data' => new TaskResource($task),
            ]);
        } catch (\Exception $e) {
            return new ErrorResource([
                'message' => 'Failed to delete comment',
                'errors' => $e->getMessage(),
                'status_code' => 500,
            ]);
        }
    }
}
