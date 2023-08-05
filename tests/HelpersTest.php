<?php

declare(strict_types=1);

use Hsndmr\CappadociaViewer\CappadociaViewer;

it('returns a new instance of CappadociaViewer', function (): void {
    $senderViewerBuilder = cappadocia();

    expect($senderViewerBuilder)->toBeInstanceOf(CappadociaViewer::class);
});
