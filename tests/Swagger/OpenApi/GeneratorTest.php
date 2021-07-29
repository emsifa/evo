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
        config([
            'evo' => [
                'openapi' => [
                    'info' => [
                        'title' => 'Test API',
                        'version' => '1.2.3',
                        'description' => 'Lorem ipsum dolor sit amet',
                        'termsOfService' => 'https://terms.url',
                        'contact' => [
                            'name' => 'John Doe',
                            'email' => 'johndoe@mail.com',
                        ],
                        'license' => [
                            'name' => 'MIT',
                            'url' => 'https://opensource.org/licenses/MIT',
                        ],
                    ],
                    'servers' => [
                        [
                            'url' => 'http://api.test.url',
                            'description' => 'Mock server',
                        ],
                    ],
                ],
            ],
        ]);

        $app = $this->app;
        $evo = $app->make('evo');
        $evo->routes(SampleSwaggerController::class);

        $generator = new Generator($app);
        $result = $generator->getResultArray();

        $this->assertArraySubset([
            'info' => [
                'title' => 'Test API',
                'version' => '1.2.3',
                'description' => 'Lorem ipsum dolor sit amet',
                'termsOfService' => 'https://terms.url',
                'contact' => [
                    'name' => 'John Doe',
                    'email' => 'johndoe@mail.com',
                ],
                'license' => [
                    'name' => 'MIT',
                    'url' => 'https://opensource.org/licenses/MIT',
                ],
            ],
            'servers' => [
                [
                    'url' => 'http://api.test.url',
                    'description' => 'Mock server',
                ],
            ],
            'paths' => [
                '/sample/stuff' => [
                    'post' => [
                        'description' => 'Post stuff endpoint',
                        'parameters' => [
                            ['name' => 'path_param', 'in' => 'path', 'description' => 'Parameter from path'],
                            ['name' => 'query_param', 'in' => 'query'],
                            ['name' => 'header_param', 'in' => 'header'],
                            ['name' => 'cookie_param', 'in' => 'cookie'],
                        ],
                        'requestBody' => [
                            'required' => true,
                            'description' => 'Post stuff data',
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
                                'description' => 'Post stuff success response',
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
