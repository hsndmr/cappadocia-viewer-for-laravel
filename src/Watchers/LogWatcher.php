<?php

declare(strict_types=1);

namespace Hsndmr\CappadociaViewer\Watchers;

use Throwable;
use Illuminate\Support\Facades\Event;
use Illuminate\Log\Events\MessageLogged;
use Hsndmr\CappadociaViewer\Enums\BadgeType;
use Hsndmr\CappadociaViewer\Enums\ViewerType;
use Hsndmr\CappadociaViewer\Facades\CappadociaViewer;

class LogWatcher extends Watcher
{
    public function register(): void
    {
        Event::listen(MessageLogged::class, [$this, 'handleLog']);
    }

    public function handleLog(MessageLogged $event): void
    {
        if (!$this->shouldHandleLog($event)) {
            return;
        }

        CappadociaViewer::setMessage($event->message)
            ->setBadge($event->level)
            ->setBadgeType(BadgeType::fromLogLevel($event->level))
            ->setType(ViewerType::LOG)
            ->send($event->context);
    }

    public function shouldHandleLog(MessageLogged $event): bool
    {
        if (!$this->isWatching()) {
            return false;
        }

        if (isset($event->context['exception']) && $event->context['exception'] instanceof Throwable) {
            return false;
        }

        return true;
    }

    protected function getConfigName(): string
    {
        return 'logs';
    }
}
