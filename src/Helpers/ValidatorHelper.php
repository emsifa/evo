<?php

namespace Emsifa\Evo\Helpers;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use ReflectionParameter;
use ReflectionProperty;

class ValidatorHelper
{
    public static function getRulesFromReflection(ReflectionProperty|ReflectionParameter $reflection): array
    {
        return [];
    }

    public static function throwValidationException(Request $request, Validator $validator)
    {
        $response = static::makeInvalidResponse($request, $validator);
        throw new ValidationException($validator, $response);
    }

    private static function makeInvalidResponse(Request $request, Validator $validator)
    {
        if ($request->wantsJson()) {
            return response()->json([
                'errors' => $validator->errors()->toArray(),
            ], 422);
        }

        return redirect()->back()->withErrors($validator->errors());
    }
}
