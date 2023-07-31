<?php

declare(strict_types=1);

namespace Hsndmr\CappadociaViewer\DataTransferObjects;

use Hsndmr\CappadociaViewer\Enums\BadgeType;
use Hsndmr\CappadociaViewer\Enums\ViewerType;

final class ViewerDto
{
    public function __construct(
        public ViewerType $type,
        public ?string $message = null,
        public ?string $badge = null,
        public ?BadgeType $badgeType = null,
        public ?array $context = null,
    ) {
    }

    public function toArray(): array
    {
        return [
            'type'      => $this->type->type(),
            'message'   => $this->message ?? '',
            'badge'     => $this->badge ?? '',
            'badgeType' => $this->badgeType?->type() ?? '',
            'context'   => $this->context ?? '',
            'timestamp' => now()->timestamp * 1000,
        ];
    }
}
