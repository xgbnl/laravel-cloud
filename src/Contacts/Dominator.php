<?php

namespace Xgbnl\Cloud\Contacts;

interface Dominator
{
    public function has(): bool;

    public function assign(string $abstract): string;

    public function getModelName(): ?string;

    public function getAlias(): string;
}
