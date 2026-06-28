<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSongRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category' => ['required', 'string', 'max:64'],
            'categoryTitle' => ['nullable', 'string', 'max:255'],
            'intake' => ['required', 'array'],
            'intake.email' => ['nullable', 'email', 'max:255'],
            'intake.*' => ['nullable'],
            'intake.anecdotesItems' => ['nullable', 'array', 'max:20'],
            'intake.anecdotesItems.*' => ['nullable', 'string', 'max:1000'],
            'intake.mustMentionItems' => ['nullable', 'array', 'max:20'],
            'intake.mustMentionItems.*' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
