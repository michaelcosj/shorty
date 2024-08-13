<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUrlRequest extends FormRequest
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
            'url' => 'required|string|max:255',
            'key' => 'required|string|max:10|unique:short_urls,key',
        ];
    }

    protected  function  prepareForValidation($attributes): array {
        $url = $attributes["url"];
        $attributes["url"] = strpos($url, 'http') !== 0 ? "http://$url" : $url;
        return $attributes;
    }
}
