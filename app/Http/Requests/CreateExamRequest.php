<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateExamRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'exam_label'=>['required'],
            'start_date'=>['required'],
            'end_date'=>['required'],
            'exam_type'=>['required'],
            'school_term'=>['required'],
            'Year'=>['required'],
            'target'=>['required'],
            'targetClasses'=>['required'],
            'targetStreams'=>['required']
        ];
    }
}
