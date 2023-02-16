<?php

namespace Xgbnl\Cloud\Contacts;

interface Factory
{
    public function get(string $abstract): mixed;
}