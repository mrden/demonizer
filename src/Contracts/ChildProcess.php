<?php

namespace Mrden\Demonizer\Contracts;

use Mrden\Forker\Contracts\Process;

abstract class ChildProcess extends Process
{
    /**
     * @var Parental|Process|null
     */
    private $parent;

    public function __construct(array $params = [], ?Parental $parentProcess = null)
    {
        $this->parent = $parentProcess;
        parent::__construct($params);
    }

    public function run(int $cloneNumber = 1): void
    {
        if ($this->parent) {
            $this->parent->setIsChildContext(true);
        }
        parent::run($cloneNumber);
    }

    protected function title(): ?string
    {
        $title = parent::title();
        if ($this->parent) {
            $parentPid = $this->parent->pid();
            if ($parentPid) {
                $title = \sprintf('%s => %s', $parentPid, $title);
            }
        }
        return $title;
    }
}
