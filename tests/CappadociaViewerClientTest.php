<?php

declare(strict_types=1);

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Hsndmr\CappadociaViewer\CappadociaViewerClient;

it('sends the data and receives a response', function (): void {
    // Arrange
    $guzzleClient = $this->mock(Client::class);

    $data = [
        'data' => 'data',
    ];

    // Act & Assert
    $guzzleClient->shouldReceive('post')
        ->once()
        ->with('/viewers', ['json' => $data])
        ->andReturn($this->mock(ResponseInterface::class));

    $cappadociaViewerClient = new CappadociaViewerClient($guzzleClient);

    $cappadociaViewerClient->send($data);
});
