<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Rules\Unique;
use Emsifa\Evo\Tests\Samples\MockPresenceVerifier;
use Illuminate\Support\Facades\Validator;

class UniqueTest extends TestCase
{
    public function testItShouldBeValidIfDataUnique()
    {
        $mockPresenceVerifier = new MockPresenceVerifier(collect([
            'users' => collect([
                ['username' => 'loremipsum'],
            ]),
        ]));

        $data = [
            'username' => 'foobar',
        ];

        $exists = new Unique('users', 'username');
        $exists->setPresenceVerifier($mockPresenceVerifier);

        $validator = Validator::make($data, [
            'username' => [$exists],
        ]);

        $this->assertFalse($validator->fails());
    }

    public function testItShouldBeInvalidIfDataDoesNotUnique()
    {
        $mockPresenceVerifier = new MockPresenceVerifier(collect([
            'users' => collect([
                ['username' => 'foobar'],
            ]),
        ]));

        $data = [
            'username' => 'foobar',
        ];

        $exists = new Unique('users', 'username');
        $exists->setPresenceVerifier($mockPresenceVerifier);

        $validator = Validator::make($data, [
            'username' => [$exists],
        ]);

        $this->assertTrue($validator->fails());
    }

    public function testOverrideMessage()
    {
        $message = 'opps invalid value';
        $rule = new Unique('users', 'username', message: $message);

        $this->assertEquals($message, $rule->message());
    }

    public function testFallbackMessage()
    {
        $rule = new Unique('users', 'username');

        $this->assertEquals(__('validation.unique'), $rule->message());
    }
}
