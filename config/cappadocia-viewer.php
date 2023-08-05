<?php

declare(strict_types=1);

return [
    'server_url'     => env('CAPPADOCIA_VIEWER_SERVER_URL', 'http://127.0.0.1:9091'),
    'timeout'        => env('CAPPADOCIA_VIEWER_TIMEOUT', 3),
    'enabled'        => env('CAPPADOCIA_VIEWER_ENABLED', true),
    'watch_logs'     => env('CAPPADOCIA_VIEWER_WATCH_LOGS', true),
    'watch_jobs'     => env('CAPPADOCIA_VIEWER_WATCH_JOBS', false),
    'watch_queries'  => env('CAPPADOCIA_VIEWER_WATCH_QUERIES', false),
    'watch_requests' => env('CAPPADOCIA_VIEWER_WATCH_REQUESTS', false),
];
