<?php

declare(strict_types=1);

namespace Hsndmr\CappadociaViewer\Builders;

use Hsndmr\CappadociaViewer\Enums\BadgeType;
use Hsndmr\CappadociaViewer\Enums\ViewerType;
use Hsndmr\CappadociaViewer\Facades\CappadociaViewer;
use Hsndmr\CappadociaViewer\DataTransferObjects\ViewerDto;

class SenderViewerBuilder
{
    protected ViewerType $type      = ViewerType::LOG;
    protected ?BadgeType $badgeType = null;
    protected ?string $badge        = null;

    public function __construct(protected string $message = '')
    {
    }

    public function setType(ViewerType $type): SenderViewerBuilder
    {
        $this->type = $type;

        return $this;
    }

    public function setBadgeType(?BadgeType $badgeType): SenderViewerBuilder
    {
        $this->badgeType = $badgeType;

        return $this;
    }

    public function setBadge(?string $badge): SenderViewerBuilder
    {
        $this->badge = $badge;

        return $this;
    }

    public function setMessage(string $message): SenderViewerBuilder
    {
        $this->message = $message;

        return $this;
    }

    public function send(array $context = []): void
    {
        CappadociaViewer::sendViewer(
            new ViewerDto(
                type: $this->type,
                message: $this->message,
                badge: $this->badge,
                badgeType: $this->badgeType,
                context: !empty($context) ? $context : null,
            )
        );
    }
}
