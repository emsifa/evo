<?php

namespace Emsifa\Evo\Http\Response;

use Attribute;
use Emsifa\Evo\Helpers\ReflectionHelper;
use Emsifa\Evo\Helpers\TypeHelper;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;
use ReflectionUnionType;
use UnexpectedValueException;

#[Attribute(Attribute::TARGET_METHOD)]
class Mock
{
    public function __construct()
    {
    }

    public function getMockedResponse(
        Container $container,
        ReflectionMethod $method,
        Request $request
    ): Responsable {
        $className = $this->getBestCandidateClassName($method);
        if (! $className || ! is_a($className, Responsable::class, true)) {
            throw new UnexpectedValueException("Cannot create mock response from class: '{$className}'");
        }

        $reflectionClass = new ReflectionClass($className);
        $mocker = new ResponseMocker($container);

        return $mocker->mock($reflectionClass, $request);
    }

    public function getBestCandidateClassName(ReflectionMethod $method): string
    {
        $returnType = $method->getReturnType();
        $isUnion = $returnType instanceof ReflectionUnionType;

        if (! $isUnion) {
            return $returnType->getName();
        }

        foreach ($returnType->getTypes() as $type) {
            if ($this->isSuccessResponse($type->getName())) {
                return $type->getName();
            }
        }

        return $returnType->getTypes()[0]->getName();
    }

    protected function isSuccessResponse(string $name): bool
    {
        if (TypeHelper::isBuiltInType($name)) {
            return false;
        }

        $statusAttrs = ReflectionHelper::getClassAttributes($name, ResponseStatus::class, ReflectionAttribute::IS_INSTANCEOF);
        if (! count($statusAttrs)) {
            return false;
        }

        /**
         * @var ResponseStatus $statusAttr
         */
        $statusAttr = $statusAttrs[0]->newInstance();
        $status = $statusAttr->getStatus();

        return $status >= 200 && $status < 300;
    }
}
