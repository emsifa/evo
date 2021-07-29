<?php

namespace Emsifa\Evo\Tests\Samples\Controllers;

use Emsifa\Evo\Http\Body;
use Emsifa\Evo\Http\Cookie;
use Emsifa\Evo\Http\File;
use Emsifa\Evo\Http\Header;
use Emsifa\Evo\Http\Param;
use Emsifa\Evo\Http\Query;
use Emsifa\Evo\Route\Post;
use Emsifa\Evo\Route\RoutePrefix;
use Emsifa\Evo\Swagger\OpenApi\Description;
use Emsifa\Evo\Tests\Samples\DTO\SwaggerPostStuffDTO;
use Emsifa\Evo\Tests\Samples\Responses\PostStuffResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Controller;

#[RoutePrefix('sample')]
class SampleSwaggerController extends Controller
{
    #[Post('stuff')]
    public function postStuff(
        #[Param('path_param'),
        Description('Parameter from path')] float $param,
        #[Query('query_param')] int $query,
        #[Body] SwaggerPostStuffDTO $dto,
        #[File] UploadedFile $file,
        #[Header('header_param')] string $header = "foo",
        #[Cookie('cookie_param')] bool $cookie = false,
    ): PostStuffResponse {
        return PostStuffResponse::fromArray([
            'id' => "1",
            'name' => "John Doe",
            'stuff' => "Lorem ipsum dolor sit amet",
        ]);
    }
}
