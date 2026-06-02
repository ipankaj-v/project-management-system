<?php

namespace App\Http\Requests\Task;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class TaskCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'task_id' => ['required', 'exists:tasks,id'],
            'body' => ['required', 'string'],
            'parent_id' => ['nullable', 'exists:task_comments,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'task_id.required' => 'The task field is required.',
            'body.required' => 'The body field is required.',
            'body.string' => 'The body must be a string.',
        ];
    }



    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422)
        );
    }
}
