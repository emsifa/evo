<?php

namespace Emsifa\Evo\Http;

use Emsifa\Evo\Helpers\ValidatorHelper;
use Emsifa\Evo\Swagger\OpenApi\Schemas\Parameter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use ReflectionParameter;
use ReflectionProperty;

abstract class CommonGetterAndValidator
{
    /**
     * @var string|array
     */
    protected $rules;

    public function __construct(
        protected string $key = '',
        protected ?string $caster = null,
        $rules = '',
    ) {
        $this->rules = $rules;
    }

    abstract public function getValue(Request $request, string $key): mixed;

    public function getKey(ReflectionParameter | ReflectionProperty $reflection): string
    {
        return $this->key ?: $reflection->getName();
    }

    public function getRequestValue(Request $request, ReflectionParameter | ReflectionProperty $reflection): mixed
    {
        $nullable = optional($reflection->getType())->allowsNull();
        $key = $this->getKey($reflection);
        $value = $this->getValue($request, $key);

        if (is_null($value) && $nullable) {
            return null;
        }

        if ($this->caster) {
            $caster = $this->caster;

            return (new $caster)->cast($value, $reflection);
        }

        return $value;
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validateRequest(Request $request, ReflectionProperty | ReflectionParameter $reflection)
    {
        $key = $this->getKey($reflection);
        $rules = $this->rules
            ? [$key => $this->rules]
            : ValidatorHelper::getRulesFromReflection($reflection, $key);

        if (! empty($rules)) {
            $data = [$key => $this->getValue($request, $key)];
            Validator::make($data, $rules)->validate();
        }
    }
}
