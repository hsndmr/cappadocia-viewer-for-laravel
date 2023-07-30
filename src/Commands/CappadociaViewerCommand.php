<?php

namespace Hsndmr\CappadociaViewer\Commands;

use Illuminate\Console\Command;

class CappadociaViewerCommand extends Command
{
    public $signature = 'cappadocia-viewer-for-laravel';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
