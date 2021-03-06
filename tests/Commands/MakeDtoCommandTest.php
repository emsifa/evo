<?php

namespace Emsifa\Evo\Tests\Commands;

use Emsifa\Evo\Tests\TestCase;

class MakeDtoCommandTest extends TestCase
{
    public function testMakeDtoFile()
    {
        $outputPath = __DIR__."/output";
        app()->useAppPath($outputPath);

        $this->artisan('evo:make-dto', [
            'file' => 'MyDto/UserDto',
            'properties' => [
                'id:int',
                'name:string',
                'email:string',
                'roles:MyDto/RoleDto[]',
                'activatedBy:?int',
                'date:DateTime',
                'numbers:int[]',
                'mixedProp',
            ],
        ]);

        $this->assertFileExists($outputPath."/Dtos/MyDto/UserDto.php");
        $this->assertFileExists($outputPath."/Dtos/MyDto/RoleDto.php");

        $this->artisan('evo:make-dto', [
            'file' => 'MyDto/UserDto',
            'properties' => [
                'id:int',
                'name:string',
                'email:string',
            ],
        ])
        ->expectsOutput("Dto 'MyDto/UserDto' already exists.");

        unlink($outputPath."/Dtos/MyDto/UserDto.php");
        unlink($outputPath."/Dtos/MyDto/RoleDto.php");
    }
}
