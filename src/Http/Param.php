<?php

namespace Emsifa\Evo\Http;

use Emsifa\Evo\Contracts\Caster;
use Emsifa\Evo\Contracts\RequestGetter;
use Emsifa\Evo\Contracts\RequestValidator;
use Emsifa\Evo\Helpers\ValidatorHelper;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use ReflectionParameter;
use ReflectionProperty;

class Param implements RequestGetter, RequestValidator
{
    /**
     * @var string|array $rules
     */
    protected $rules;

    public function __construct(
        protected string $key = '',
        protected ?Caster $caster = null,
        $rules = '',
    )
    {
        $this->rules = $rules;
    }

    public function getRequestValue(Request $request, ReflectionParameter|ReflectionProperty $reflection): mixed
    {
        $nullable = optional($reflection->getType())->allowsNull();
        $key = $this->key ?: $reflection->getName();
        $value = $request->route($key);

        if (is_null($value) && $nullable) {
            return null;
        }

        if ($this->caster) {
            return $this->caster->cast($value, $reflection);
        }

        return $value;
    }

    public function validateRequest(Request $request, ReflectionProperty|ReflectionParameter $reflection)
    {
        $key = $this->key ?: $reflection->getName();
        $rules = $this->rules ?: ValidatorHelper::getRulesFromReflection($reflection);

        if (!empty($rules)) {
            $validator = FacadesValidator::make([$key => $request->route($key)], [$key => $rules]);
            $validator->validate();
            if ($validator->fails()) {
                ValidatorHelper::throwValidationException($request, $validator);
            }
        }
    }
}
