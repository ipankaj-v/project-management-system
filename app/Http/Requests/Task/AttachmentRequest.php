<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class AttachmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'task_id' => ['required', 'exists:tasks,id'],
            'file' => ['required', 'file', 'max:10240', 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,jpg,jpeg,png,gif,txt'],
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'A file is required',
            'file.file' => 'The uploaded data must be a file',
            'file.max' => 'File size must not exceed 10MB',
            'file.mimes' => 'File must be a valid document, image, or archive type',
        ];
    }
}
