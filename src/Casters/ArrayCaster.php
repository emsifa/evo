<?php

namespace Emsifa\Evo\Casters;

use Emsifa\Evo\Contracts\Caster;
use Emsifa\Evo\Exceptions\CastErrorException;
use Emsifa\Evo\Helpers\ReflectionHelper;
use Emsifa\Evo\ObjectFiller;
use Emsifa\Evo\Types\ArrayOf;
use Illuminate\Support\Collection;
use ReflectionAttribute;
use ReflectionParameter;
use ReflectionProperty;

class ArrayCaster implements Caster
{
    public function cast($value, ReflectionProperty|ReflectionParameter $prop): mixed
    {
        $nullable = optional($prop->getType())->allowsNull();
        if ($nullable && is_null($value)) {
            return null;
        }

        if (is_array($value)) {
            return $this->castIfNeeded($value, $prop);
        }

        if (is_object($value) && $value instanceof Collection) {
            return $this->castIfNeeded($value->toArray(), $prop);
        }

        throw new CastErrorException("Cannot cast 'array' from type: ".gettype($value).'.');
    }

    protected function castIfNeeded(array $value, ReflectionProperty $prop)
    {
        /**
         * @var ArrayOf $arrayOf
         */
        $arrayOf = ReflectionHelper::getFirstAttributeInstance($prop, ArrayOf::class, ReflectionAttribute::IS_INSTANCEOF);

        if (!$arrayOf) {
            return $value;
        }

        $type = $arrayOf->getType();
        $ifCastError = $arrayOf->getIfCastError();

        $results = [];
        foreach ($value as $val) {
            try {
                $results[] = $this->castValue($val, $type, $prop);
            } catch (\Exception $err) {
                switch ($ifCastError) {
                    case ArrayOf::SKIP_ITEM:
                        break;
                    case ArrayOf::NULL_ITEM:
                        $results[] = null;
                        break;
                    case ArrayOf::KEEP_AS_IS:
                        $results[] = $val;
                        break;
                    default: throw $err;
                }
            }
        }
        return $results;
    }

    public function castValue($value, string $type, ReflectionProperty $prop)
    {
        $casters = ObjectFiller::getCasters($prop->getDeclaringClass()->getName());

        if (array_key_exists($type, $casters)) {
            /**
             * @var \Emsifa\Evo\Contracts\Caster $caster
             */
            $caster = new $casters[$type];
            return $caster->cast($value, $prop);
        }

        if (is_array($value) && class_exists($type)) {
            $obj = new $type;
            ObjectFiller::fillObject($obj, $value);
            return $obj;
        }

        throw new CastErrorException("Cannot cast value to '{$type}' from type: ".gettype($value));
    }
}
