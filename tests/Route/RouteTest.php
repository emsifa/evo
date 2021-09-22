<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Route\Delete;
use Emsifa\Evo\Route\Get;
use Emsifa\Evo\Route\Patch;
use Emsifa\Evo\Route\Post;
use Emsifa\Evo\Route\Put;
use Emsifa\Evo\Route\Route;

class RouteTest extends TestCase
{
    /**
     * @dataProvider routesProvider
     */
    public function testCreateRoute(Route $route, $expect)
    {
        $this->assertEquals($expect[0], $route->methods());
        $this->assertEquals($expect[1], $route->uri());
        $this->assertEquals($expect[2], $route->middleware());
        $this->assertEquals($expect[3], $route->getName());
        $this->assertEquals($expect[4], $route->getDomain());
    }

    public function routesProvider()
    {
        return [
            [new Route('GET', ''), [['GET', 'HEAD'], '', [], null, null]],
            [new Route('POST', '/stuff'), [['POST'], '/stuff', [], null, null]],
            [new Route('PUT', '/stuff', 'auth'), [['PUT'], '/stuff', ['auth'], null, null]],
            [new Route('PATCH', '/stuff', name: 'stuff.put'), [['PATCH'], '/stuff', [], 'stuff.put', null]],
            [new Route('DELETE', '/stuff', domain: 'domain.name'), [['DELETE'], '/stuff', [], null, 'domain.name']],
            [new Route('GET', '/stuff', 'auth', 'stuff', 'domain.name'), [['GET', 'HEAD'], '/stuff', ['auth'], 'stuff', 'domain.name']],

            [new Get('/foo', 'auth', 'foo.get', 'foo.domain.name'), [['GET', 'HEAD'], '/foo', ['auth'], 'foo.get', 'foo.domain.name']],
            [new Post('/foo', 'auth', 'foo.post', 'foo.domain.name'), [['POST'], '/foo', ['auth'], 'foo.post', 'foo.domain.name']],
            [new Put('/foo', 'auth', 'foo.put', 'foo.domain.name'), [['PUT'], '/foo', ['auth'], 'foo.put', 'foo.domain.name']],
            [new Patch('/foo', 'auth', 'foo.patch', 'foo.domain.name'), [['PATCH'], '/foo', ['auth'], 'foo.patch', 'foo.domain.name']],
            [new Delete('/foo', 'auth', 'foo.delete', 'foo.domain.name'), [['DELETE'], '/foo', ['auth'], 'foo.delete', 'foo.domain.name']],
        ];
    }
}
