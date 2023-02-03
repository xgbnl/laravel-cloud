<?php

namespace Xgbnl\Cloud\Decorates\Factory;

use Xgbnl\Cloud\Decorates\ArrayDecorate;
use Xgbnl\Cloud\Decorates\Contacts\Decorate;
use Xgbnl\Cloud\Decorates\Contacts\ImageObjectDecorate;
use Xgbnl\Cloud\Decorates\StringDecorate;

readonly class DecorateFactory
{
    static public function builderDecorate(mixed $type): Decorate|ImageObjectDecorate
    {
        return match (true) {
            is_string($type) => new StringDecorate(),
            is_array($type)  => new ArrayDecorate(),
        };
    }
}