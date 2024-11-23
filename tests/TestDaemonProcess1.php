<?php

namespace Tests;

use Mrden\Demonizer\Contracts\DaemonProcess;
use Mrden\Forker\Contracts\Storage;
use Mrden\Forker\Storage\FileStorage;

class TestDaemonProcess1 extends DaemonProcess
{
    /**
     * @psalm-var positive-int
     */
    protected $maxCloneCount = 15;

    protected $nameProcess = 'Тестовый процесс';

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
            throw new \Exception('Param "test-param" required');
        }
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
