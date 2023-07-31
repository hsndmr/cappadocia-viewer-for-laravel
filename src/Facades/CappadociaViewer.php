<?php

declare(strict_types=1);

namespace Hsndmr\CappadociaViewer\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Hsndmr\CappadociaViewer\CappadociaViewer
 */
class CappadociaViewer extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Hsndmr\CappadociaViewer\CappadociaViewer::class;
    }
}
