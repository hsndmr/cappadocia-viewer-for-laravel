<?php

declare(strict_types=1);

use Illuminate\View\View;
use Mockery\MockInterface;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Event;
use Illuminate\Database\Eloquent\Model;
use Hsndmr\CappadociaViewer\FormatModel;
use Hsndmr\CappadociaViewer\CappadociaViewer;
use Hsndmr\CappadociaViewer\Enums\ViewerType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\HeaderBag;
use Illuminate\Http\Response as IlluminateResponse;
use Hsndmr\CappadociaViewer\Watchers\RequestWatcher;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;

it('returns sessions as array if request has sessions', function (): void {
    // Arrange
    $watcher = new RequestWatcher();

    $requestMock = $this->mock(Request::class);

    $requestMock->shouldReceive('hasSession')->andReturn(true);

    $requestMock->shouldReceive('session->all')
        ->andReturn([
            'foo' => 'bar',
        ]);

    $getSessionsReflectionMethod = new ReflectionMethod(RequestWatcher::class, 'getSessions');

    // Act
    $sessions = $getSessionsReflectionMethod->invoke($watcher, $requestMock);

    // Assert
    expect($sessions)->toEqual([
        'foo' => 'bar',
    ]);
});

it('returns sessions as empty array if request has no sessions', function (): void {
    // Arrange
    $watcher = new RequestWatcher();

    $requestMock = $this->mock(Request::class);

    $requestMock->shouldReceive('hasSession')->andReturnFalse();

    $getSessionsReflectionMethod = new ReflectionMethod(RequestWatcher::class, 'getSessions');

    // Act
    $sessions = $getSessionsReflectionMethod->invoke($watcher, $requestMock);

    // Assert
    expect($sessions)->toEqual([]);
});

it('it returns config name correctly', function (): void {
    // Arrange
    $watcher                    = new RequestWatcher();
    $configNameReflectionMethod = new ReflectionMethod(RequestWatcher::class, 'getConfigName');

    // Act
    $configName = $configNameReflectionMethod->invoke($watcher);

    // Assert
    expect($configName)->toEqual('requests');
});

it('extracts data from view correctly', function (): void {
    // Arrange
    $modelMock = $this->mock(Model::class);

    $modelMock->shouldReceive('getKey')
        ->andReturn(1);

    $mockClass = new class {
        public string $property = 'property_value';
    };

    $viewMock = $this->mock(View::class);

    $viewMock->shouldReceive('getData')
        ->andReturn([
            $modelMock,
            $mockClass,
            ['test'],
        ]);

    $viewer = new RequestWatcher();

    $extractDataFromViewReflectionMethod = new ReflectionMethod(
        RequestWatcher::class,
        'extractDataFromView'
    );

    // Act
    $data = $extractDataFromViewReflectionMethod->invoke(
        $viewer,
        $viewMock,
    );

    // Assert
    expect($data)->toEqual([
        FormatModel::given($modelMock),
        [
            'class'      => get_class($mockClass),
            'properties' => [
                'property' => 'property_value',
            ],
        ],
        ['test'],
    ]);

});

it('formats response correctly', function (): void {
    // Arrange
    $watcher                        = new RequestWatcher();
    $formatResponseReflectionMethod = new ReflectionMethod(RequestWatcher::class, 'formatResponse');

    $jsonResponse = new Response(json_encode(['key' => 'value']));
    $jsonResponse->headers->set('Content-Type', 'application/json');

    $textResponse = new Response('plain text');
    $textResponse->headers->set('Content-Type', 'text/plain');

    $redirectResponse = new RedirectResponse('http://example.com');

    $viewMock = $this->mock(View::class);
    $viewMock->shouldReceive('getPath')
        ->andReturn('path/to/view');
    $viewMock->shouldReceive('getData')
        ->andReturn([
            ['key' => 'value'],
        ]);
    $illuminateResponseMock = $this->mock(IlluminateResponse::class);
    $illuminateResponseMock
        ->shouldReceive('getOriginalContent')
        ->andReturn($viewMock);
    $illuminateResponseMock
        ->shouldReceive('getContent')
        ->andReturn(false);

    $emptyResponse = new Response('');

    $htmlResponse = new Response('<html></html>');
    $htmlResponse->headers->set('Content-Type', 'text/html');

    // Act
    $jsonResult                   = $formatResponseReflectionMethod->invoke($watcher, $jsonResponse);
    $resultTextResponse           = $formatResponseReflectionMethod->invoke($watcher, $textResponse);
    $resultRedirectResponse       = $formatResponseReflectionMethod->invoke($watcher, $redirectResponse);
    $resultHtmlResponse           = $formatResponseReflectionMethod->invoke($watcher, $htmlResponse);
    $resultEmptyResponse          = $formatResponseReflectionMethod->invoke($watcher, $emptyResponse);
    $resultIlluminateResponseMock = $formatResponseReflectionMethod->invoke($watcher, $illuminateResponseMock);

    // Assert
    expect($jsonResult)->toEqual(['key' => 'value'])
        ->and($resultTextResponse)->toEqual('plain text')
        ->and($resultRedirectResponse)->toEqual('Redirected to http://example.com')
        ->and($resultIlluminateResponseMock)->toEqual([
            'view' => 'path/to/view',
            'data' => [
                ['key' => 'value'],
            ],
        ])
        ->and($resultHtmlResponse)->toEqual('HTML Response')
        ->and($resultEmptyResponse)->toEqual('Empty Response');
});

it('extracts input correctly', function (): void {
    // Arrange
    $watcher                      = new RequestWatcher();
    $extractInputReflectionMethod = new ReflectionMethod(RequestWatcher::class, 'extractInput');

    $uploadedFileMock = $this->mock(UploadedFile::class);
    $uploadedFileMock->shouldReceive('getClientOriginalName')->andReturn('file.txt');
    $uploadedFileMock->shouldReceive('isFile')->andReturn(true);
    $uploadedFileMock->shouldReceive('getSize')->andReturn(1000);

    $request = new Request();
    $request->files->set('file_field', $uploadedFileMock);

    // Act
    $result = $extractInputReflectionMethod->invoke($watcher, $request);

    // Assert
    expect($result)->toEqual([
        'file_field' => [
            'name' => 'file.txt',
            'size' => '1KB',
        ],
    ]);
});

it('gets data correctly', function (): void {
    // Arrange
    $request = $this->mock(Request::class, function (MockInterface $mock): void {
        $route = $this->mock(Route::class, function (MockInterface $mock): void {
            $mock->shouldReceive('getActionName')->andReturn('Controller@action');
            $mock->shouldReceive('gatherMiddleware')->andReturn(['middleware']);
        });

        $headerBag = $this->mock(HeaderBag::class);
        $headerBag->shouldReceive('all')->andReturn(['headerKey' => 'headerValue']);

        $mock->shouldReceive('server')->with('REQUEST_TIME_FLOAT')->andReturn(microtime(true));
        $mock->headers = $headerBag;
        $mock->shouldReceive('ip')->andReturn('127.0.0.1');
        $mock->shouldReceive('root')->andReturn('http://example.com');
        $mock->shouldReceive('fullUrl')->andReturn('http://example.com/test');
        $mock->shouldReceive('method')->andReturn('GET');
        $mock->shouldReceive('route')->andReturn($route);
    });

    $response = $this->mock(Response::class, function (MockInterface $mock): void {
        $mock->shouldReceive('getStatusCode')->andReturn(200);
    });

    $requestHandled = new RequestHandled($request, $response);

    $watcher = $this->partialMock(RequestWatcher::class)
        ->shouldAllowMockingProtectedMethods()
        ->makePartial();

    $watcher->shouldReceive('formatResponse')->andReturn('formattedResponse');
    $watcher->shouldReceive('extractInput')->andReturn(['inputKey' => 'inputValue']);
    $watcher->shouldReceive('getSessions')->andReturn(['sessionKey' => 'sessionValue']);

    $getDataReflectionMethod = new ReflectionMethod(RequestWatcher::class, 'getData');

    // Act
    $result = $getDataReflectionMethod->invoke($watcher, $requestHandled);

    // Assert
    expect($result)
        ->toHaveKeys([
            'duration',
            'memory',
        ])
        ->and($result['status'])->toEqual(['200'])
        ->and($result['response'])->toEqual('formattedResponse')
        ->and($result['headers'])->toEqual(['headerKey' => 'headerValue'])
        ->and($result['payload'])->toEqual(['inputKey' => 'inputValue'])
        ->and($result['ip'])->toEqual('127.0.0.1')
        ->and($result['session'])->toEqual(['sessionKey' => 'sessionValue'])
        ->and($result['controller'])->toEqual('Controller@action')
        ->and($result['middleware'])->toEqual(['middleware'])
        ->and($result['uri'])->toEqual('/test')
        ->and($result['method'])->toEqual('GET');
});

it('registers the event listener correctly', function (): void {
    // Arrange
    $watcher = new RequestWatcher();

    // Act & Assert
    Event::partialMock()
        ->shouldReceive('listen')
        ->with(RequestHandled::class, [$watcher, 'handleRequest'])
        ->once();

    $watcher->register();
});

it('handles request correctly when watching', function (): void {
    // Arrange
    $watcher = $this->partialMock(RequestWatcher::class)
        ->shouldAllowMockingProtectedMethods()
        ->makePartial();

    $watcher->shouldReceive('isWatching')
        ->andReturnTrue();

    $watcher->shouldReceive('getData')
        ->andReturn([
            'uri'    => '/test',
            'method' => 'GET',
        ]);

    $event = $this->mock(RequestHandled::class);

    // Act & Assert
    $this->mock(CappadociaViewer::class,
        function (MockInterface $mock): void {
            $mock->shouldReceive('setMessage')
                ->with('/test')
                ->andReturnSelf()
                ->once();

            $mock->shouldReceive('setBadge')
                ->with('GET')
                ->andReturnSelf()
                ->once();

            $mock->shouldReceive('setType')
                ->with(ViewerType::QUERY)
                ->andReturnSelf()
                ->once();

            $mock->shouldReceive('send')
                ->andReturnNull()
                ->once();
        }
    );

    /* @var RequestWatcher $watcher */
    $watcher->handleRequest($event);
});

it('does not handle request when not watching', function (): void {
    // Arrange
    $watcher = $this->partialMock(RequestWatcher::class)
        ->shouldAllowMockingProtectedMethods()
        ->makePartial();

    $watcher->shouldReceive('isWatching')
        ->andReturnFalse();

    $event = $this->mock(RequestHandled::class);

    // Act & Assert
    $this->mock(CappadociaViewer::class)
        ->shouldReceive('send')
        ->never();

    /* @var RequestWatcher $watcher */
    $watcher->handleRequest($event);
});
