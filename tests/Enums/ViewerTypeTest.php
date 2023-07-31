<?php

declare(strict_types=1);

use Hsndmr\CappadociaViewer\Enums\ViewerType;

it('can get the correct type for ViewerType', function (): void {
    expect(ViewerType::LOG->type())->toBe('log')
        ->and(ViewerType::JOB->type())->toBe('job');
});
