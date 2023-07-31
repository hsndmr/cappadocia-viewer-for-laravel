<?php

declare(strict_types=1);

return [
    'server_url' => env('CAPPADOCIA_VIEWER_SERVER_URL', 'http://127.0.0.1:9091'),
    'timeout'    => env('CAPPADOCIA_VIEWER_TIMEOUT', 3),
    'enabled'    => env('CAPPADOCIA_VIEWER_ENABLED', true),
];
