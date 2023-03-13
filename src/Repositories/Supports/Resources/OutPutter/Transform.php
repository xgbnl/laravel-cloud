<?php

namespace Xgbnl\Cloud\Repositories\Supports\Resources\OutPutter;

class Transform extends OutPutter
{
    protected ?string $call = null;

    public function getValue(): ?string
    {
        return $this->call;
    }

    protected function configure(int|string $value): void
    {
        $this->call = $value;
    }
}