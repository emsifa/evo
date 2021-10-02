<?php

namespace Emsifa\Evo\Tests\Commands;

use Emsifa\Evo\Commands\MakeResponseCommand;
use Emsifa\Evo\Http\Response\JsonResponse;
use Emsifa\Evo\Http\Response\UseJsonTemplate;
use Emsifa\Evo\Http\Response\ViewResponse;
use Emsifa\Evo\Tests\TestCase;
use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\BufferedOutput;

class MakeResponseCommandTest extends TestCase
{
    public function testMakeJsonResponseFile()
    {
        $outputPath = __DIR__."/output";
        app()->useAppPath($outputPath);

        $this->artisan('evo:make-response', [
            'file' => 'User/CreateUserResponse',
            'properties' => [
                'id:int',
                'name:string',
                'email:string',
                'roles:User/RoleData[]',
                'activatedBy:?int',
            ],
        ]);

        $this->assertFileExists($outputPath."/Http/Responses/User/CreateUserResponse.php");
        $this->assertFileExists($outputPath."/Http/Responses/User/RoleData.php");

        $this->assertFileContains($outputPath."/Http/Responses/User/CreateUserResponse.php", "use ".JsonResponse::class.";");
        $this->assertFileContains($outputPath."/Http/Responses/User/CreateUserResponse.php", "class CreateUserResponse extends JsonResponse");

        unlink($outputPath."/Http/Responses/User/CreateUserResponse.php");
        unlink($outputPath."/Http/Responses/User/RoleData.php");
    }

    public function testMakeViewResponseFile()
    {
        $outputPath = __DIR__."/output";
        app()->useAppPath($outputPath);

        $this->artisan('evo:make-response', [
            'file' => 'Todo/TodosViewResponse',
            'properties' => [
                'id:int',
                'title:string',
                'completed:bool',
                'user:Todo/UserData',
            ],
            '--view' => true,
        ]);

        $this->assertFileExists($outputPath."/Http/Responses/Todo/TodosViewResponse.php");
        $this->assertFileExists($outputPath."/Http/Responses/Todo/UserData.php");

        $this->assertFileContains($outputPath."/Http/Responses/Todo/TodosViewResponse.php", "use ".ViewResponse::class.";");
        $this->assertFileContains($outputPath."/Http/Responses/Todo/TodosViewResponse.php", "class TodosViewResponse extends ViewResponse");

        unlink($outputPath."/Http/Responses/Todo/TodosViewResponse.php");
        unlink($outputPath."/Http/Responses/Todo/UserData.php");
    }

    public function testMakeJsonResponseWithTemplate()
    {
        $outputPath = __DIR__."/output";
        app()->useAppPath($outputPath);

        $this->artisan('evo:make-response', [
            'file' => 'Post/CreatePostResponse',
            'properties' => [
                'id:int',
                'title:string',
                'body:string',
                'categories:Post/CategoryData[]',
            ],
            '--json-template' => 'MyTemplate',
        ]);

        $this->assertFileExists($outputPath."/Http/Responses/Post/CreatePostResponse.php");
        $this->assertFileExists($outputPath."/Http/Responses/Post/CategoryData.php");

        $this->assertFileContains($outputPath."/Http/Responses/Post/CreatePostResponse.php", "use ".JsonResponse::class.";");
        $this->assertFileContains($outputPath."/Http/Responses/Post/CreatePostResponse.php", "class CreatePostResponse extends JsonResponse");
        $this->assertFileContains($outputPath."/Http/Responses/Post/CreatePostResponse.php", "use ".UseJsonTemplate::class.";");
        $this->assertFileContains($outputPath."/Http/Responses/Post/CreatePostResponse.php", "#[UseJsonTemplate(MyTemplate::class)]");

        unlink($outputPath."/Http/Responses/Post/CreatePostResponse.php");
        unlink($outputPath."/Http/Responses/Post/CategoryData.php");
    }

    public function assertFileContains($file, $text)
    {
        $content = file_get_contents($file);
        $this->assertStringContainsString($text, $content, "File '{$file}' doesn't contains text: {$text}");
    }
}
