<?php

namespace Emsifa\Evo\Tests\Samples;

use Emsifa\Evo\Casters\IntCaster;
use Emsifa\Evo\Dto\UseCaster;
use Emsifa\Evo\Types\CollectionOf;
use Illuminate\Support\Collection;

#[UseCaster('int', IntCaster::class)]
class SampleCollection
{
    public Collection $mixedCollection;

    #[CollectionOf('int')]
    public Collection $collectionOfInt;

    #[CollectionOf('int', ifCastError: CollectionOf::THROW_ERROR)]
    public Collection $collectionOfIntThrownError;

    #[CollectionOf('int', ifCastError: CollectionOf::SKIP_ITEM)]
    public Collection $collectionOfIntSkipError;

    #[CollectionOf('int', ifCastError: CollectionOf::NULL_ITEM)]
    public Collection $collectionOfIntNullOnError;

    #[CollectionOf('int', ifCastError: CollectionOf::KEEP_AS_IS)]
    public Collection $collectionOfIntKeepAsIs;
}
