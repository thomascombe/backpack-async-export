<?php

namespace Thomascombe\BackpackAsyncExport\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportRequest extends FormRequest
{
    const PARAM_FILE = 'file';

    public function rules(): array
    {
        return [
            self::PARAM_FILE => [
                'required',
                'file',
            ],
        ];
    }

    public function authorize(): bool
    {
        return backpack_auth()->check();
    }
}
