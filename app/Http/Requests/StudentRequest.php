<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "name" => "required|string|max:255",
            "email" => "required|email|unique:students,email," . $this->email,
            'date_of_birth' => 'nullable|date',
            'father_name' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'اسم ضروری است.',
            'email.required' => 'The email field is required.',
            'email.unique' => 'The email has already been taken.',
            'date_of_birth.date' => 'The date of birth must be a valid date.',
            'father_name.string' => 'The father name must be a string.',
        ];
    }
}
