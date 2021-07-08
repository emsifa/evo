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
            'MyDTO/UserDTO',
            'id:int',
            'name:string',
            'email:string',
            'roles:MyDTO/RoleDTO[]',
            'activatedBy:?int',
        ]);
        $output = new BufferedOutput();

        $outputPath = __DIR__."/output";
        app()->useAppPath($outputPath);

        $command->run($input, $output);

        $this->assertFileExists($outputPath."/DTO/MyDTO/UserDTO.php");
        $this->assertFileExists($outputPath."/DTO/MyDTO/RoleDTO.php");

        unlink($outputPath."/DTO/MyDTO/UserDTO.php");
        unlink($outputPath."/DTO/MyDTO/RoleDTO.php");
    }
}
