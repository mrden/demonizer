<?php

namespace Tests;

use Mrden\Demonizer\Contracts\ChildDaemonProcess;
use Mrden\Forker\Contracts\PidStorage;
use Mrden\Forker\Storage\FilePidStorage;

class TestChildDaemonProcess extends ChildDaemonProcess
{
    /**
     * @psalm-var positive-int
     */
    protected int $maxCloneCount = 15;

    protected string|null $nameProcess = 'Тестовый процесс';
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
            throw new \Exception('Param "test-param" required');
        }
    }

    protected function prepare(): void
    {
    }

    protected function pidStorage(): PidStorage
    {
        if ($this->pidStorage === null) {
            $this->pidStorage = new FilePidStorage($this, __DIR__ . '/../.mrden');
        }
        return $this->pidStorage;
    }
}
