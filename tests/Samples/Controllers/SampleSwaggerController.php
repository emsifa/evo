<?php

namespace Emsifa\Evo\Tests\Samples\Controllers;

use Emsifa\Evo\Http\Body;
use Emsifa\Evo\Http\Cookie;
use Emsifa\Evo\Http\File;
use Emsifa\Evo\Http\Header;
use Emsifa\Evo\Http\Param;
use Emsifa\Evo\Http\Query;
use Emsifa\Evo\Http\Response\Mock;
use Emsifa\Evo\Route\Post;
use Emsifa\Evo\Route\RoutePrefix;
use Emsifa\Evo\Swagger\OpenApi\Description;
use Emsifa\Evo\Swagger\OpenApi\Example;
use Emsifa\Evo\Swagger\OpenApi\Summary;
use Emsifa\Evo\Tests\Samples\DTO\SwaggerPostStuffDTO;
use Emsifa\Evo\Tests\Samples\Responses\PostStuffResponse;
use Emsifa\Evo\Tests\Samples\Responses\SampleErrorResponse;
use Emsifa\Evo\Tests\Samples\Responses\SampleNotFoundResponse;
use Emsifa\Evo\Tests\Samples\Responses\SampleSuccessResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Controller;

#[RoutePrefix('sample')]
class SampleSwaggerController extends Controller
{
    #[Post('stuff')]
    #[Mock(optional: true)]
    #[Summary('Post Stuff')]
    #[Description('Post stuff endpoint')]
    public function postStuff(
        #[Param('path_param'),
        Description('Parameter from path')] float $param,
        #[Query('query_param'),
        Example('query value')] int $query,
        #[Body] SwaggerPostStuffDTO $dto,
        #[File] UploadedFile $file,
        #[Header('header_param'),
        Example('header value')] string $header = "foo",
        #[Cookie('cookie_param'),
        Example('klepon')] bool $cookie = false,
    ): PostStuffResponse {
        return PostStuffResponse::fromArray([
            'id' => "1",
            'name' => "John Doe",
            'stuff' => "Lorem ipsum dolor sit amet",
        ]);
    }

    #[Post('multiple-response')]
    public function multipleResponse(): SampleSuccessResponse | SampleNotFoundResponse | SampleErrorResponse
    {
        return new SampleNotFoundResponse();
    }
}
