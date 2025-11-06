<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->is_admin ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'location' => ['nullable', 'string', 'max:255'],
            'highlight_needs' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp,avif', 'max:5120'],
        ];
    }
}