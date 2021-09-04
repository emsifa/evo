<?php

namespace Emsifa\Evo\Http;

use Emsifa\Evo\Helpers\ReflectionHelper;
use Emsifa\Evo\Helpers\ValidatorHelper;
use Emsifa\Evo\ValidationData;
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

    abstract public function hasValue(Request $request, string $key): mixed;

    public function getKey(ReflectionParameter | ReflectionProperty $reflection): string
    {
        return $this->key ?: $reflection->getName();
    }

    public function getRequestValue(Request $request, ReflectionParameter | ReflectionProperty $reflection): mixed
    {
        $key = $this->getKey($reflection);

        $hasDefaultValue = ReflectionHelper::hasDefaultValue($reflection);
        if (! $this->hasValue($request, $key) && $hasDefaultValue) {
            return ReflectionHelper::getDefaultValue($reflection);
        }

        $value = $this->getValue($request, $key);
        $nullable = optional($reflection->getType())->allowsNull();
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
        $data = [$key => $this->getValue($request, $key)];
        $rules = $this->rules
            ? [$key => $this->rules]
            : ValidatorHelper::getRulesFromReflection($reflection, $key, new ValidationData($data));

        if (! empty($rules)) {
            Validator::make($data, $rules)->validate();
        }
    }
}
