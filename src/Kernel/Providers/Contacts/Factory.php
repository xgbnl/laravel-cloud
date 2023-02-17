<?php

namespace Xgbnl\Cloud\Kernel\Providers\Contacts;

use Xgbnl\Cloud\Contacts\Contextual;

interface Factory extends Provideable
{
    public function get(Contextual $contextual, string $name): mixed;
}