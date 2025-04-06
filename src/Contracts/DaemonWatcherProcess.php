<?php

namespace Mrden\Demonizer\Contracts;

use Mrden\Demonizer\Exceptions\DemonizeException;
use Mrden\Forker\Contracts\Titled;
use Mrden\Forker\Exceptions\ForkException;
use Mrden\Forker\Forker;

abstract class DaemonWatcherProcess extends MainDaemonProcess implements Parental, Titled
{
    private bool $isChildContext = false;

    final public function maxCloneCount(): int
    {
        return 1;
    }

    public function stop(?callable $afterStop = null): void
    {
        parent::stop(function () use ($afterStop) {
            foreach ($this->children() as $process) {
                $processObject = $this->createProcess($process);
                $forker = new Forker($processObject);
                $forker->stop(Forker::STOP_ALL);
            }
            if ($afterStop !== null) {
                $afterStop();
            }
        });
    }

    /**
     * @throws DemonizeException
     * @throws ForkException
     */
    protected function job(): void
    {
        foreach ($this->children() as $process) {
            $processObject = $this->createProcess($process);
            $forker = new Forker($processObject);
            $count = $process['count'] ?? 1;
            $forker->run($count);
        }
    }

    public function getTitle(): string
    {
        $childrenCount = \count($this->children());
        return \sprintf(
            '%s %s',
            ($this->nameProcess ?? \get_class($this)) . $this->paramsToString(),
            ' (' . $childrenCount . ' children)'
        );
    }

    protected function checkParams(): void
    {
    }

    /**
     * @psalm-param array{process:class-string<ChildProcess>, params?:array, count?: int} $process
     * @throws DemonizeException
     */
    private function createProcess(array $process): ChildProcess
    {
        if (!isset($process['process'])) {
            throw new DemonizeException('Incorrect process config');
        }
        if (!\class_exists($process['process'])) {
            throw new DemonizeException('Not found process ' . $process['process']);
        }
        if (!\is_subclass_of($process['process'], ChildProcess::class)) {
            throw new DemonizeException('Incorrect implementation child process ' . $process['process']);
        }
        return new $process['process']($this, $process['params'] ?? []);
    }

    public function setIsChildContext(bool $isChildContext): void
    {
        $this->isChildContext = $isChildContext;
    }

    public function shutdownHandler(int $number): void
    {
        if (!$this->isChildContext) {
            parent::shutdownHandler($number);
        }
    }
}
