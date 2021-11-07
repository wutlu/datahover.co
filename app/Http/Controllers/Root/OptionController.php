<?php

namespace App\Http\Controllers\Root;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Models\Option;

class OptionController extends Controller
{
    protected $rules;

    public function __construct()
    {
        $this->rules = [
            'twitter.status' => 'required|string|in:on,off'
        ];
    }

    public function update(Request $request)
    {
        $request->validate(
            [
                'key' => 'required|exists:options,key',
                'value' => @$this->rules[$request->key] ?? 'nullable|string|max:255'
            ]
        );

        (new Option)->change($request->key, $request->value);

        return [
            'success' => 'ok',
            'alert' => [
                'type' => 'success',
                'message' => 'Option updated',
            ]
        ];
    }
}
