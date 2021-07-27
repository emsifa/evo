<?php

namespace Emsifa\Evo\Tests\OpenApi;

use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
use Emsifa\Evo\Swagger\OpenApi\Generator;
use Emsifa\Evo\Tests\Samples\Controllers\SampleSwaggerController;
use Emsifa\Evo\Tests\TestCase;

class GeneratorTest extends TestCase
{
    use ArraySubsetAsserts;

    /**
     * @test
     */
    public function testResolveParameters()
    {
        $app = $this->app;
        $evo = $app->make('evo');
        $evo->routes(SampleSwaggerController::class);

        $generator = new Generator($app);
        $result = $generator->getResultArray();

        $this->assertArraySubset([
            'paths' => [
                'sample/stuff' => [
                    'post' => [
                        'parameters' => [
                            ['name' => 'path_param', 'in' => 'path'],
                            ['name' => 'query_param', 'in' => 'query'],
                            ['name' => 'header_param', 'in' => 'header'],
                            ['name' => 'cookie_param', 'in' => 'cookie'],
                        ],
                        'requestBody' => [
                            'required' => true,
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        '$ref' => '#/components/schemas/EmsifaEvoTestsSamplesDTOSwaggerPostStuffDTO',
                                    ],
                                ],
                            ],
                        ],
                        'responses' => [
                            '200' => [
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/EmsifaEvoTestsSamplesResponsesPostStuffResponse',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'components' => [
                'schemas' => [
                    'EmsifaEvoTestsSamplesDTOSwaggerPostStuffDTO' => [
                        'type' => 'object',
                        'required' => ['age', 'name', 'email'],
                        'properties' => [
                            'age' => ['type' => 'integer'],
                            'name' => ['type' => 'string'],
                            'email' => ['type' => 'string'],
                            'child' => [
                                'type' => 'object',
                                'properties' => [
                                    'thing' => ['type' => 'string'],
                                    'numbers' => [
                                        'type' => 'array',
                                        'items' => [
                                            'type' => 'integer',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'EmsifaEvoTestsSamplesResponsesPostStuffResponse' => [
                        'type' => 'object',
                        'properties' => [
                            'status' => ['type' => 'integer'],
                            'data' => [
                                'type' => 'object',
                                'properties' => [
                                    'id' => ['type' => 'integer'],
                                    'name' => ['type' => 'string'],
                                    'stuff' => ['type' => 'string'],
                                    'relation' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'thing' => ['type' => 'string'],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ], $result);
    }
}
