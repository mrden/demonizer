<?php

namespace Mrden\Demonizer\Contracts;

trait DaemonProcessTrait
{
    protected float $period = 0.2;
    protected bool $isExecute = true;
    protected int $memoryLimitInBytes = 32 * 1024 * 1024;

    public function stop(?callable $afterStop = null): void
    {
        $this->isExecute = false;
        parent::stop($afterStop);
    }

    public function execute(): void
    {
        while ($this->isExecute) {
            \pcntl_signal_dispatch();
            // Restore pid in storage every iteration
            $pid = $this->pid($this->getRunningCloneNumber());
            if ($pid === null) {
                $this->pidStorage()->save($this->getRunningCloneNumber(), \getmypid());
            }
            $this->job();
            \usleep(\max((int) $this->period * 1000000, 1000));
            if ($this->memoryLimitInBytes && \memory_get_usage() > $this->memoryLimitInBytes) {
                $this->restart();
            }
        }
    }
}
