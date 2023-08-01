<?php

declare(strict_types=1);

use Hsndmr\CappadociaViewer\Builders\SenderViewerBuilder;

if (!function_exists('cappadocia')) {
    function cappadocia(string $message = ''): SenderViewerBuilder
    {
        return new SenderViewerBuilder($message);
    }
}
