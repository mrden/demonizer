<?php

use Tests\TestChildDaemonProcess;

return [
    [
        'process' => TestChildDaemonProcess::class,
        'params' => ['test-param' => 5],
        'count' => 2,
    ],
    [
        'process' => TestChildDaemonProcess::class,
        'params' => ['test-param' => 8],
        'count' => 1,
    ],
];
