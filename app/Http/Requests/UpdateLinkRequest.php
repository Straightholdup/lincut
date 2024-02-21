<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(@OA\Xml(name="UpdateLinkRequest"))
 */
class UpdateLinkRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @OA\Property
     *
     * @var string
     */
    public $original_url;

    /**
     * @OA\Property
     *
     * @var string
     */
    public $short_token;

    /**
     * @OA\Property
     *
     * @var boolean
     */
    public $is_private;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $method = $this->method();
        if ($method == 'PUT') {
            return [
                'original_url' => 'required|url',
                'short_token' => 'required|unique:links,short_token',
                'is_private' => 'boolean',
            ];
        }

        return [
            'original_url' => 'sometimes|required|url',
            'short_token' => 'sometimes|required|unique:links,short_token,' . $this->link->id,
            'is_private' => 'sometimes|boolean',
        ];
    }
}
