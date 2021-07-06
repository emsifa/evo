<?php

namespace Emsifa\Evo\Http;

use Attribute;
use Emsifa\Evo\Contracts\RequestGetter;
use Emsifa\Evo\Contracts\RequestValidator;
use Emsifa\Evo\DTO;
use Emsifa\Evo\Helpers\ValidatorHelper;
use Emsifa\Evo\ObjectFiller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use ReflectionClass;
use ReflectionParameter;
use ReflectionProperty;

#[Attribute(Attribute::TARGET_PROPERTY + Attribute::TARGET_PARAMETER)]
class Body implements RequestGetter, RequestValidator
{
    /**
     * @var string|array
     */
    protected $rules;

    public function __construct(protected ?string $caster = null, $rules = '') {
        $this->rules = $rules;
    }

    public function getKey(ReflectionParameter | ReflectionProperty $reflection): string
    {
        return $this->key ?: $reflection->getName();
    }

    public function getRequestValue(Request $request, ReflectionParameter | ReflectionProperty $reflection): mixed
    {
        $typeName = optional($reflection->getType())->getName();

        if (!$typeName) {
            return $this->getMergedInputsAndFiles($request);
        }

        if ($typeName && class_exists($typeName) && is_subclass_of($typeName, DTO::class)) {
            return $typeName::fromRequest($request);
        }

        $data = $this->getMergedInputsAndFiles($request);
        $object = new $typeName;
        ObjectFiller::fillObject($object, $data);

        return $object;
    }

    protected function getMergedInputsAndFiles(Request $request): array
    {
        $inputs = $request->all();
        $files = Arr::dot($request->allFiles());
        foreach ($files as $key => $file) {
            Arr::set($inputs, $key, $file);
        }

        return $inputs;
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validateRequest(Request $request, ReflectionProperty | ReflectionParameter $reflection)
    {
        $typeName = optional($reflection->getType())->getName();

        if (!$typeName) {
            return;
        }

        $rules = ValidatorHelper::getRulesFromClass(new ReflectionClass($typeName));

        if (!empty($rules)) {
            $data = $this->getMergedInputsAndFiles($request);
            Validator::make($data, $rules)->validate();
        }
    }
}
