<?php

namespace Mrden\Demonizer\Contracts;

abstract class DaemonProcess extends ChildProcess
{
    protected float $period = 0.2;
    protected int $memoryLimit = -1;
    private bool $isExecute = true;

    protected function initGracefulShutdown(): void
    {
        $this->isExecute = false;
    }

    public function execute(): void
    {
        while ($this->isExecute) {
            $this->getProcessManager()->dispatchSignals();
            $this->updateTitle();
            // Restore pid in storage every iteration
            $pid = $this->getPidStorage()->get($this->getRunningCloneNumber());
            if (!$pid) {
                $this->getPidStorage()->save($this->getRunningCloneNumber(), $this->getProcessManager()->getCurrentPid());
            }

            $this->job();

            if ($this->memoryLimit > 0 && \memory_get_usage() > $this->memoryLimit) {
                if ($this->getParentPid()) {
                    $this->initGracefulShutdown();
                } else {
                    $this->initRestartMySelf();
                }
            }

            \usleep($this->period * 1000000);
        }
    }

    protected function updateTitle(): void
    {
        $usedMemoryBytes = \memory_get_usage(true);
        $usedMemoryMb = \round($usedMemoryBytes / 1024 / 1024, 2);
        \cli_set_process_title(\sprintf('%s [mem %sMb]', $this->getTitle(), $usedMemoryMb));
    }

    abstract protected function job(): void;
}
