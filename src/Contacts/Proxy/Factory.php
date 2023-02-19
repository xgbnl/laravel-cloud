<?php

namespace Xgbnl\Cloud\Contacts\Proxy;

use Xgbnl\Cloud\Contacts\Controller\Contextual;

interface Factory extends Proxyable
{
    public function get(Contextual $contextual, string $name): mixed;
}