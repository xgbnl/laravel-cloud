<?php

namespace Xgbnl\Cloud\Contacts;

interface Factory
{
    public function resolve(string $abstract): mixed;
}