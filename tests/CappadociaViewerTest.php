<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\PendingRequest;
use Hsndmr\CappadociaViewer\CappadociaViewer;
use Hsndmr\CappadociaViewer\Enums\ViewerType;
use Hsndmr\CappadociaViewer\DataTransferObjects\ViewerDto;

it('sends a POST request to the specified server URL', function (): void {
    // Arrange
    $url = config('cappadocia-viewer.server_url').'/viewer';
    $dto = new ViewerDto(ViewerType::LOG);

    Http::fake([
        $url => Http::response([]),
    ]);

    $cappadociaViewer = new CappadociaViewer();

    // Act
    $cappadociaViewer->sendViewer($dto);

    // Assert
    Http::assertSent(function ($request) use ($url) {
        return $request->url() == $url && $request->method() == 'POST';
    });

});

it('does not send a POST request when the server is unavailable', function (): void {
    // Arrange
    $cappadociaViewer = new CappadociaViewer();
    $url              = config('cappadocia-viewer.server_url').'/viewer';
    $dto              = new ViewerDto(ViewerType::LOG);

    Http::fake([
        $url => Http::response([], 500),
    ]);

    $cappadociaViewer->sendViewer($dto);

    Http::fake([
        $url => Http::response([]),
    ]);

    // Act
    $cappadociaViewer->sendViewer($dto);

    // Assert
    Http::assertSentCount(1);
});

it('configures the HTTP timeout according to the configuration', function (): void {
    // Arrange
    $timeout = config('cappadocia-viewer.timeout');

    $cappadociaViewer = new CappadociaViewer();

    // Act
    $http = $cappadociaViewer->http();

    // Assert
    expect($http)->toBeInstanceOf(PendingRequest::class)
        ->and($http->getOptions()['timeout'])->toBe($timeout);
});
