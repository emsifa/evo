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
    public function testCreateRoute()
    {
        // IDK why when we use @dataProvider,
        // Route constructor are not covered
        // So we put it here instead
        $tests = [
            [new Route('GET', ''), [['GET', 'HEAD'], '', [], null, null]],
            [new Route('POST', '/stuff'), [['POST'], '/stuff', [], null, null]],
            [new Route('PUT', '/stuff', 'auth'), [['PUT'], '/stuff', ['auth'], null, null]],
            [new Route('PATCH', '/stuff', name: 'stuff.put'), [['PATCH'], '/stuff', [], 'stuff.put', null]],
            [new Route('DELETE', '/stuff', domain: 'domain.name'), [['DELETE'], '/stuff', [], null, 'domain.name']],
            [new Route('GET', '/stuff', 'auth', 'stuff', 'domain.name', ['id' => '/\d+/i']), [['GET', 'HEAD'], '/stuff', ['auth'], 'stuff', 'domain.name']],

            [new Get('/foo', 'auth', 'foo.get', 'foo.domain.name'), [['GET', 'HEAD'], '/foo', ['auth'], 'foo.get', 'foo.domain.name']],
            [new Post('/foo', 'auth', 'foo.post', 'foo.domain.name'), [['POST'], '/foo', ['auth'], 'foo.post', 'foo.domain.name']],
            [new Put('/foo', 'auth', 'foo.put', 'foo.domain.name'), [['PUT'], '/foo', ['auth'], 'foo.put', 'foo.domain.name']],
            [new Patch('/foo', 'auth', 'foo.patch', 'foo.domain.name'), [['PATCH'], '/foo', ['auth'], 'foo.patch', 'foo.domain.name']],
            [new Delete('/foo', 'auth', 'foo.delete', 'foo.domain.name'), [['DELETE'], '/foo', ['auth'], 'foo.delete', 'foo.domain.name']],
        ];

        foreach ($tests as $test) {
            $this->assertEquals($test[1][0], $test[0]->methods());
            $this->assertEquals($test[1][1], $test[0]->uri());
            $this->assertEquals($test[1][2], $test[0]->middleware());
            $this->assertEquals($test[1][3], $test[0]->getName());
            $this->assertEquals($test[1][4], $test[0]->getDomain());
        }
    }

    public function testSetUses()
    {
        $route = new Route('GET', '/foo');
        $route->setUses("Foo@bar");

        $this->assertEquals('Foo@bar', $route->getAction('uses'));
        $this->assertEquals('Foo@bar', $route->getAction('controller'));
    }
}
