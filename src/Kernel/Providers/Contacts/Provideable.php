<?php

namespace Xgbnl\Cloud\Kernel\Providers\Contacts;

interface Provideable
{
    public function refresh(string $class): string;

    public function getAccessor(): string;

    public function has(): bool;
}