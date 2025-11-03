<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRestaurantUserRequest extends FormRequest
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
        $restaurantUser = $this->route('restaurant_user');
        $restaurantUserId = $restaurantUser ? $restaurantUser->id : null;

        return [
            'username' => 'sometimes|required|string|min:2|max:255',
            'mobile_number' => 'sometimes|required|string|regex:/^[\+]?[0-9\s\-\(\)]{10,20}$/',
            'total_users_count' => 'sometimes|nullable|integer|min:1',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Clean mobile number by removing spaces, dashes, parentheses
        if ($this->has('mobile_number')) {
            $cleanNumber = preg_replace('/[\s\-\(\)]/', '', $this->mobile_number);
            $this->merge([
                'mobile_number' => $cleanNumber,
            ]);
        }
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'username.required' => 'Person name is required.',
            'username.min' => 'Person name must be at least 2 characters.',
            'username.max' => 'Person name cannot exceed 255 characters.',
            'mobile_number.required' => 'Mobile number is required.',
            'mobile_number.regex' => 'Mobile number must be 10-20 characters and contain only numbers, spaces, dashes, or parentheses.',

            'total_users_count.integer' => 'Total users count must be a number.',
            'total_users_count.min' => 'Total users count must be at least 1.',
        ];
    }
}
