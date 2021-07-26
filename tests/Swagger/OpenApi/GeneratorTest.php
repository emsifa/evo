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
                    ]
                ]
            ]
        ], $result);
    }
}
