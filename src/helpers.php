<?php

declare(strict_types=1);

use Hsndmr\CappadociaViewer\CappadociaViewer;
use Hsndmr\CappadociaViewer\Facades\CappadociaViewer as Cappadocia;

if (!function_exists('cappadocia')) {
    function cappadocia(string $message = ''): CappadociaViewer
    {
        return Cappadocia::setMessage($message);
    }
}
