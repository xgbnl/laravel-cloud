<?php

namespace Xgbnl\Cloud\Repositories\Supports\Resources\OutPutter;

class Chunkable extends OutPutter
{
    protected int $count = 200;

    public function getValue(): int
    {
        return $this->count;
    }

    protected function configure(int|string $value): void
    {
        $this->count = $value;
    }
}