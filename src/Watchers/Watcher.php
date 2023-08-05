<?php

declare(strict_types=1);

namespace Hsndmr\CappadociaViewer\Watchers;

abstract class Watcher
{
    protected bool $isWatching = false;

    abstract public function register(): void;

    abstract protected function getConfigName(): string;

    public function isWatching(): bool
    {
        if (config('cappadocia-viewer.watch_'.$this->getConfigName(), false)) {
            return true;
        }

        if ($this->isWatching) {
            return true;
        }

        return false;
    }

    public function watch(): self
    {
        $this->isWatching = true;

        return $this;
    }

    public function stopWatching(): self
    {
        $this->isWatching = false;

        return $this;
    }
}
