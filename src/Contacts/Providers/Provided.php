<?php

namespace Xgbnl\Cloud\Contacts\Providers;

interface Provided
{
    /**
     * Setting or reset current proxy class name.
     * @param string $class
     * @return string
     */
    public function refresh(string $class): string;

    /**
     * Get proxy model name.
     * @return string
     */
    public function getAccessor(): string;

    /**
     * Check proxy model is exists.
     * @return bool
     */
    public function has(): bool;
}