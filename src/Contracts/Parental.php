<?php

namespace Mrden\Demonizer\Contracts;

interface Parental
{
    public function setIsChildContext(bool $isChildContext): void;

    /**
     * @psalm-return list<array{process:class-string<ChildProcess>, params?:array, count?: int}>
     */
    public function children(): array;
}
