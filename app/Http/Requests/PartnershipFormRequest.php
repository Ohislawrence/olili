<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class PartnershipFormRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'company_name' => 'required|string|max:200',
            'industry' => 'required|string|max:100',
            'company_size' => 'required|string|max:50',
            'csr_budget' => 'required|string|max:50',
            'contact_name' => 'required|string|max:150',
            'job_title' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'phone' => 'required|string|max:20',
            'interests' => 'required|array|min:1',
            'interests.*' => 'string|in:csr_sponsorship,corporate_volunteerism,corporate_giving,employee_matching',
            'message' => 'required|string|min:20|max:3000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'company_name.required' => 'Please enter your company name.',
            'contact_name.required' => 'Please enter your full name.',
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'phone.required' => 'Please enter your phone number.',
            'interests.required' => 'Please select at least one partnership interest.',
            'interests.min' => 'Please select at least one partnership interest.',
            'message.required' => 'Please tell us about your CSR goals.',
            'message.min' => 'Please provide more details about your CSR goals (minimum 20 characters).',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Ensure interests is always an array
        if ($this->has('interests') && !is_array($this->interests)) {
            $this->merge([
                'interests' => [$this->interests]
            ]);
        }
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation errors',
            'errors' => $validator->errors()
        ], 422));
    }
}