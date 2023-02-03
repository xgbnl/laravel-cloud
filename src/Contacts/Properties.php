<?php

namespace Xgbnl\Cloud\Contacts;

interface Properties
{
    public function isNull(): bool;

    public function assign(string $abstract): string;

    public function getModelName(): ?string;

    public function getCalledClass(): string;
}
