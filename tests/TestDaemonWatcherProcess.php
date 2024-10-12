<?php

namespace Tests;

use Mrden\Demonizer\Contracts\DaemonWatcherProcess;
use Mrden\Forker\Contracts\Storage;
use Mrden\Forker\Storage\FileStorage;

class TestDaemonWatcherProcess extends DaemonWatcherProcess
{
    protected $period = 5;

    public function children(): array
    {
        return include __DIR__ . '/config.php';
    }

    protected function prepare(): void
    {
    }

    protected function pidStorage(): Storage
    {
        if (!isset($this->pidStorage)) {
            $this->pidStorage = new FileStorage($this, __DIR__ . '/../.mrden');
        }
        return $this->pidStorage;
    }
}
