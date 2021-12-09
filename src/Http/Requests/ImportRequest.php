<?php

namespace Thomascombe\BackpackAsyncExport\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

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
        return Auth::guard(backpack_guard_name())->check();
    }
}
