<?php

namespace Emsifa\Evo\Tests\Commands;

use Emsifa\Evo\Commands\MakeDtoCommand;
use Emsifa\Evo\Tests\TestCase;
use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\BufferedOutput;

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
            ],
        ]);

        $this->assertFileExists($outputPath."/Dtos/MyDto/UserDto.php");
        $this->assertFileExists($outputPath."/Dtos/MyDto/RoleDto.php");

        unlink($outputPath."/Dtos/MyDto/UserDto.php");
        unlink($outputPath."/Dtos/MyDto/RoleDto.php");
    }
}
