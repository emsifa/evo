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
                '/sample/stuff' => [
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
                                'multipart/form-data' => [
                                    'schema' => [
                                        '$ref' => '#/components/schemas/Emsifa.Evo.Tests.Samples.Controllers.SampleSwaggerController.postStuff',
                                    ],
                                ],
                            ],
                        ],
                        'responses' => [
                            '200' => [
                                'description' => 'OK',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/Emsifa.Evo.Tests.Samples.Responses.PostStuffResponse',
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
                    'Emsifa.Evo.Tests.Samples.Controllers.SampleSwaggerController.postStuff' => [
                        'type' => 'object',
                        'required' => ['age', 'name', 'email', 'child', 'file'],
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
                            'file' => [
                                'type' => 'string',
                                'format' => 'binary',
                            ],
                        ],
                    ],
                    'Emsifa.Evo.Tests.Samples.Responses.PostStuffResponse' => [
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
