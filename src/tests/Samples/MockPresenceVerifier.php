<?php

namespace Emsifa\Evo\Tests\Samples;

use Illuminate\Support\Collection;
use Illuminate\Validation\PresenceVerifierInterface;

class MockPresenceVerifier implements PresenceVerifierInterface
{
    public function __construct(protected Collection $database)
    {
    }

    public function getCount($collection, $column, $value, $excludeId = null, $idColumn = null, array $extra = [])
    {
        $collection = $this->database->get($collection) ?: collect([]);
        return $collection->where($column, $value)->count();
    }

    public function getMultiCount($collection, $column, array $values, array $extra = [])
    {

    }
}
