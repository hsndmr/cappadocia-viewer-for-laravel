<?php

declare(strict_types=1);
use Hsndmr\CappadociaViewer\Builders\SenderViewerBuilder;

it('returns a new instance of SenderViewerBuilder', function (): void {
    $senderViewerBuilder = cappadocia();

    expect($senderViewerBuilder)->toBeInstanceOf(SenderViewerBuilder::class);
});
