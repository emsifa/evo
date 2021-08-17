<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Rules\Exists;
use Emsifa\Evo\Tests\Samples\MockPresenceVerifier;
use Illuminate\Database\Connection;
use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\PresenceVerifierInterface;
use Mockery;

class ExistsTest extends TestCase
{
    public function testItShouldBeValidIfDataExists()
    {
        $mockPresenceVerifier = new MockPresenceVerifier(collect([
            'users' => collect([
                ['username' => 'foobar'],
            ]),
        ]));

        $data = [
            'username' => 'foobar',
        ];

        $exists = new Exists('users', 'username');
        $exists->setPresenceVerifier($mockPresenceVerifier);

        $validator = Validator::make($data, [
            'username' => [$exists],
        ]);

        $this->assertFalse($validator->fails());
    }

    public function testItShouldBeInvalidIfDataDoesNotExists()
    {
        $mockPresenceVerifier = new MockPresenceVerifier(collect([
            'users' => collect([
                ['username' => 'foobar'],
            ]),
        ]));

        $data = [
            'username' => 'loremipsum',
        ];

        $exists = new Exists('users', 'username');
        $exists->setPresenceVerifier($mockPresenceVerifier);

        $validator = Validator::make($data, [
            'username' => [$exists],
        ]);

        $this->assertTrue($validator->fails());
    }

    public function testOverrideMessage()
    {
        $message = 'opps invalid value';
        $rule = new Exists('users', 'username', message: $message);

        $this->assertEquals($message, $rule->message());
    }

    public function testFallbackMessage()
    {
        $rule = new Exists('users', 'username');

        $this->assertEquals(__('validation.exists'), $rule->message());
    }
}
