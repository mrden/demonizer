<?php

namespace Mrden\Demonizer\Contracts;

use Mrden\Forker\Contracts\Process;
use Mrden\Forker\Contracts\ProcessManagerInterface;
use Mrden\Forker\Contracts\Titled;

abstract class ChildProcess extends Process implements Titled
{
    /**
     * @var int|null
     */
    private int|null $parentPid;

    public function __construct(
        array $params = [],
        ?ProcessManagerInterface $processManager = null,
        ?string $pidStorageClassName = null,
        ?int $parentPid = null
    ) {
        $this->parentPid = $parentPid;
        parent::__construct($params, $processManager, $pidStorageClassName);
    }

    public function getTitle(): string
    {
        $title = $this->getDefaultTitle();
        if ($this->parentPid) {
            $title = \sprintf('%s => %s', $this->parentPid, $title);
        }
        return $title;
    }

    public function getParentPid(): ?int
    {
        return $this->parentPid;
    }
}
