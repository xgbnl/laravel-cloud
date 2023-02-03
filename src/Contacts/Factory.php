<?php

namespace Xgbnl\Cloud\Contacts;

interface Factory
{
    public function make(string $abstract): mixed;
}