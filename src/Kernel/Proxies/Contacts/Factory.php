<?php

namespace Xgbnl\Cloud\Kernel\Proxies\Contacts;

use Xgbnl\Cloud\Contacts\Contextual;

interface Factory extends Proxyable
{
    public function get(Contextual $contextual, string $name): mixed;
}