<?php

use Tests\TestDaemonProcess;
use Tests\TestDaemonProcess1;

return [
    [
        'process' => TestDaemonProcess::class,
        'params' => ['test-param' => 5],
        'count' => 2,
    ],
    [
        'process' => TestDaemonProcess1::class,
        'params' => ['test-param' => 8],
        'count' => 1,
    ],
];
