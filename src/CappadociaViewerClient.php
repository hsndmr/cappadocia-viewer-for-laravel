<?php

declare(strict_types=1);

namespace Hsndmr\CappadociaViewer;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\GuzzleException;

class CappadociaViewerClient
{
    public function __construct(
        protected Client $client
    ) {
    }

    /**
     * @throws GuzzleException
     */
    public function send(array $data): ResponseInterface
    {
        return $this->client->post('/viewers', [
            'json' => $data,
        ]);
    }
}
