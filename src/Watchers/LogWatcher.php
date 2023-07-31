<?php

declare(strict_types=1);

namespace Hsndmr\CappadociaViewer\Watchers;

use Throwable;
use Illuminate\Support\Facades\Event;
use Illuminate\Log\Events\MessageLogged;
use Hsndmr\CappadociaViewer\Enums\BadgeType;
use Hsndmr\CappadociaViewer\Enums\ViewerType;
use Hsndmr\CappadociaViewer\Facades\CappadociaViewer;
use Hsndmr\CappadociaViewer\DataTransferObjects\ViewerDto;

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

        CappadociaViewer::sendViewer(new ViewerDto(
            type: ViewerType::LOG,
            message: $event->message,
            badge: $event->level,
            badgeType: BadgeType::fromLogLevel($event->level),
            context: empty($event->context) ? null : $event->context,
        ));

    }

    public function shouldHandleLog(MessageLogged $event): bool
    {
        if (isset($event->context['exception']) && $event->context['exception'] instanceof Throwable) {
            return false;
        }

        return true;
    }
}
