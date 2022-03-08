<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;

class ResizeImageRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        $rules = [
            'image' => 'required|',
            'w' => ['required', 'regex:/^\d+(\.\d+)?%?$/'], // 50,50%,50.123,50.125%
            'h' => 'regex:/^\d+(\.\d+)?%?$/',
            'album_id' => 'exists:albums,id'
        ];

        $image = $this->all()['image'] ?? false;

        $rules['image'] .= ($image && $image instanceof UploadedFile) ? 'image' : 'url';

        return $rules;
    }
}
