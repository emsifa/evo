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
        $command = new MakeDtoCommand(new Filesystem);
        $command->setLaravel(new Container);

        $input = new ArgvInput([
            'evo:make-dto',
            'MyDto/UserDto',
            'id:int',
            'name:string',
            'email:string',
            'roles:MyDto/RoleDto[]',
            'activatedBy:?int',
        ]);
        $output = new BufferedOutput();

        $outputPath = __DIR__."/output";
        app()->useAppPath($outputPath);

        $command->run($input, $output);

        $this->assertFileExists($outputPath."/Dto/MyDto/UserDto.php");
        $this->assertFileExists($outputPath."/Dto/MyDto/RoleDto.php");

        unlink($outputPath."/Dto/MyDto/UserDto.php");
        unlink($outputPath."/Dto/MyDto/RoleDto.php");
    }
}
