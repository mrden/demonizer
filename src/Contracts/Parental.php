<?php

namespace Mrden\Demonizer\Contracts;

interface Parental
{
    /**
     * @psalm-return list<array{process:class-string<ChildProcess>, params?:array, count?: int}>
     */
    public function children(): array;
}
