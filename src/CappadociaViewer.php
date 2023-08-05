<?php

declare(strict_types=1);

namespace Hsndmr\CappadociaViewer;

use Throwable;
use Hsndmr\CappadociaViewer\Enums\BadgeType;
use Hsndmr\CappadociaViewer\Enums\ViewerType;
use Hsndmr\CappadociaViewer\Watchers\QueryWatcher;
use Hsndmr\CappadociaViewer\DataTransferObjects\ViewerDto;

class CappadociaViewer
{
    protected bool $isServerAvailable = true;
    protected ViewerType $type        = ViewerType::LOG;
    protected ?BadgeType $badgeType   = null;
    protected ?string $badge          = null;
    protected string $message         = '';

    public function setType(ViewerType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function setBadgeType(?BadgeType $badgeType): self
    {
        $this->badgeType = $badgeType;

        return $this;
    }

    public function setBadge(?string $badge): self
    {
        $this->badge = $badge;

        return $this;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    protected function resetData(): void
    {
        $this->type      = ViewerType::LOG;
        $this->badgeType = null;
        $this->badge     = null;
        $this->message   = '';
    }

    public function watchQueries(): self
    {
        app(QueryWatcher::class)->watch();

        return $this;
    }

    public function stopWatchingQueries(): self
    {
        app(QueryWatcher::class)->stopWatching();

        return $this;
    }

    public function send(array $context = []): void
    {
        $viewerDto = new ViewerDto(
            type: $this->type,
            message: $this->message,
            badge: $this->badge,
            badgeType: $this->badgeType,
            context: !empty($context) ? $context : null,
        );

        try {
            app(CappadociaViewerClient::class)->send($viewerDto->toArray());
        } catch (Throwable) {
            $this->isServerAvailable = false;
        }

        $this->resetData();
    }
}
