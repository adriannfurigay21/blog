<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'user_id' => 'required|integer|exists:users,id',
            'title' => 'required|string|max:50',
            'body' => 'required|string',
            'summary' => 'required|string',
            'tags' => 'required|string|max:50',
            'image' => 'required|file|mimetypes:image/jpeg, image/png, image/jpg'
        ];
    }
}
