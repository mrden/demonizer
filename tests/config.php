<?php

use Tests\TestDaemonProcess;

return [
    [
        'process' => TestDaemonProcess::class,
        'params' => ['test-param' => 5],
        'count' => 2,
    ],
    [
        'process' => TestDaemonProcess::class,
        'params' => ['test-param' => 8],
        'count' => 1,
    ],
];
