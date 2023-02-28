<?php

namespace Xgbnl\Cloud\Contacts\Proxy;

use Xgbnl\Cloud\Contacts\Controller\Contextual;

interface Factory extends Proxyable
{
    /**
     * Get dynamic proxy object.
     * @param Contextual $contextual
     * @param string $name
     * @return mixed
     */
    public function get(Contextual $contextual, string $name): mixed;
}