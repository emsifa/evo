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
                    'security_schemes' => [
                        'jwt' => [
                            "type" => "http",
                            "scheme" => "bearer",
                            "bearerFormat" => "JWT",
                        ],
                        'web' => [
                            "type" => "http",
                            "scheme" => "basic",
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
                        'summary' => 'Post Stuff',
                        'description' => 'Post stuff endpoint',
                        'parameters' => [
                            ['name' => 'path_param', 'in' => 'path', 'description' => 'Parameter from path'],
                            ['name' => 'query_param', 'in' => 'query', 'example' => 'query value'],
                            ['name' => 'header_param', 'in' => 'header', 'example' => 'header value'],
                            ['name' => 'cookie_param', 'in' => 'cookie', 'example' => 'klepon'],
                            ['name' => '_mock', 'in' => 'query', 'schema' => ['default' => 0, 'example' => 1]],
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
                        'security' => [
                            ['web' => []],
                            ['jwt' => []],
                        ],
                    ],
                ],
                '/sample/multiple-response' => [
                    'post' => [
                        'responses' => [
                            '201' => [
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/Emsifa.Evo.Tests.Samples.Responses.SampleSuccessResponse',
                                        ],
                                    ],
                                ],
                            ],
                            '404' => [
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/Emsifa.Evo.Tests.Samples.Responses.SampleNotFoundResponse',
                                        ],
                                    ],
                                ],
                            ],
                            '500' => [
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/Emsifa.Evo.Tests.Samples.Responses.SampleErrorResponse',
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
                            'age' => ['type' => 'integer', 'example' => 18],
                            'name' => ['type' => 'string', 'example' => 'John Doe'],
                            'email' => ['type' => 'string', 'example' => 'johndoe@mail.com'],
                            'child' => [
                                'type' => 'object',
                                'properties' => [
                                    'thing' => ['type' => 'string', 'example' => 'A thing'],
                                    'numbers' => [
                                        'type' => 'array',
                                        'items' => [
                                            'type' => 'integer',
                                        ],
                                        'example' => [1, 2, 3],
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
                                    'id' => ['type' => 'integer', 'example' => 1],
                                    'name' => ['type' => 'string', 'example' => 'Lorem Ipsum'],
                                    'stuff' => ['type' => 'string', 'example' => 'A stuff'],
                                    'relation' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'thing' => ['type' => 'string', 'example' => 'Relation thing'],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'Emsifa.Evo.Tests.Samples.Responses.SampleSuccessResponse' => [
                        'type' => 'object',
                        'properties' => [
                            'id' => ['type' => 'integer'],
                            'name' => ['type' => 'string'],
                        ],
                    ],
                    'Emsifa.Evo.Tests.Samples.Responses.SampleNotFoundResponse' => [
                        'type' => 'object',
                        'properties' => [
                            'message' => ['type' => 'string'],
                        ],
                    ],
                    'Emsifa.Evo.Tests.Samples.Responses.SampleErrorResponse' => [
                        'type' => 'object',
                        'properties' => [
                            'message' => ['type' => 'string'],
                        ],
                    ],
                ],
                'securitySchemes' => [
                    'jwt' => [
                        "type" => "http",
                        "scheme" => "bearer",
                        "bearerFormat" => "JWT",
                    ],
                    'web' => [
                        "type" => "http",
                        "scheme" => "basic",
                    ],
                ],
            ],
        ], $result);
    }
}
