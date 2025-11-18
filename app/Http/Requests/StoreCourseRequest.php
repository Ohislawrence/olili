<?php
// app/Http/Requests/StoreCourseRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourseRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string',
            'exam_board_id' => 'nullable|exists:exam_boards,id',
            'learning_objectives' => 'nullable|array',
            'learning_objectives.*' => 'string|max:500',
        ];
    }
}
