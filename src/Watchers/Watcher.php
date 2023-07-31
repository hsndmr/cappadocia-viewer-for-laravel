<?php

declare(strict_types=1);

namespace Hsndmr\CappadociaViewer\Watchers;

abstract class Watcher
{
    abstract public function register(): void;
}
