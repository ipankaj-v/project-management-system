<?php

namespace App\Http\Controllers\Api\Task;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\AttachmentRequest;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\SuccessResource;
use App\Http\Resources\Task\TaskResource;
use App\Models\Attachment;
use App\Models\Task;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    public function store(AttachmentRequest $request)
    {
        $validated = $request->validated();
        $task = Task::find($validated['task_id']);

        if (!$task) {
            return new ErrorResource(['message' => 'Task not found', 'status_code' => 404]);
        }

        try {
            $file = $request->file('file');
            $path = Storage::disk('public')->put('attachments', $file);

            if (!$path) {
                return new ErrorResource([
                    'message' => 'Failed to store file',
                    'status_code' => 500,
                ]);
            }

            $attachment = Attachment::create([
                'attachable_type' => Task::class,
                'attachable_id' => $task->id,
                'user_id' => $request->user()?->id,
                'filename' => $file->getClientOriginalName(),
                'path' => $path,
                'mime' => $file->getClientMimeType(),
                'size' => $file->getSize(),
            ]);

            $task->load('project', 'assigned', 'comments');

            return new SuccessResource([
                'message' => 'File attached successfully',
                'data' => new TaskResource($task),
                'status_code' => 201,
            ]);
        } catch (\Exception $e) {
            return new ErrorResource([
                'message' => 'Failed to attach file',
                'errors' => $e->getMessage(),
                'status_code' => 500,
            ]);
        }
    }

    public function destroy($id)
    {
        $attachment = Attachment::find($id);

        if (!$attachment) {
            return new ErrorResource(['message' => 'Attachment not found', 'status_code' => 404]);
        }

        try {
            Storage::disk('public')->delete($attachment->path);
            $attachable = $attachment->attachable;
            $attachment->delete();

            if ($attachable instanceof Task) {
                $attachable->load('project', 'assigned', 'comments');
                return new SuccessResource([
                    'message' => 'Attachment deleted',
                    'data' => new TaskResource($attachable),
                ]);
            }

            return new SuccessResource(['message' => 'Attachment deleted']);
        } catch (\Exception $e) {
            return new ErrorResource([
                'message' => 'Failed to delete attachment',
                'errors' => $e->getMessage(),
                'status_code' => 500,
            ]);
        }
    }
}
