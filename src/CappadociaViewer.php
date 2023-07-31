<?php

declare(strict_types=1);

namespace Hsndmr\CappadociaViewer;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\PendingRequest;
use Hsndmr\CappadociaViewer\DataTransferObjects\ViewerDto;

class CappadociaViewer
{
    protected bool $isServerAvailable = true;

    public function sendViewer(ViewerDto $viewerDto): void
    {
        if (!$this->isServerAvailable) {
            return;
        }

        try {
            $this->http()->post('viewer', $viewerDto->toArray());
        } catch (\Throwable $th) {
            $this->isServerAvailable = false;
        }
    }

    public function http(): PendingRequest
    {
        return Http::baseUrl(config('cappadocia-viewer.server_url'))
            ->timeout(config('cappadocia-viewer.timeout'));
    }
}
