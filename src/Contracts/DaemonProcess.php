<?php

namespace Mrden\Demonizer\Contracts;

abstract class DaemonProcess extends ChildProcess
{
    protected $period = 0.2;
    protected $isExecute = true;
    protected $memoryLimit;

    public function stop(?callable $afterStop = null): void
    {
        $this->isExecute = false;
        parent::stop($afterStop);
    }

    public function execute(): void
    {
        while ($this->isExecute) {
            // Restore pid in storage every iteration
            $pid = $this->pid($this->getRunningCloneNumber());
            if (!$pid) {
                $this->pidStorage()->save($this->getRunningCloneNumber(), \getmypid());
            }
            $this->job();
            \usleep($this->period * 1000000);
            if ($this->memoryLimit && \memory_get_usage() > $this->memoryLimit) {
                $this->restart();
            }
        }
    }

    protected function updateTitle(string $message): void
    {
        \cli_set_process_title(\sprintf('%s %s', $this->title(), $message));
    }

    abstract protected function job(): void;
}
