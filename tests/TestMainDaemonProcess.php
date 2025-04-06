<?php

namespace Tests;

use Mrden\Demonizer\Contracts\MainDaemonProcess;
use Mrden\Forker\Contracts\PidStorage;
use Mrden\Forker\Storage\FilePidStorage;

class TestMainDaemonProcess extends MainDaemonProcess
{
    /**
     * @psalm-var positive-int
     */
    protected int $maxCloneCount = 15;

    private FilePidStorage|null $pidStorage = null;

    protected function job(): void
    {
        \sleep(2);
    }

    /**
     * @throws \Exception
     */
    protected function checkParams(): void
    {
        if (!isset($this->params)) {
            throw new \Exception('Params required');
        }
    }

    protected function prepare(): void
    {
    }

    protected function pidStorage(): PidStorage
    {
        if (!isset($this->pidStorage)) {
            $this->pidStorage = new FilePidStorage($this, __DIR__ . '/../.mrden');
        }
        return $this->pidStorage;
    }
}
