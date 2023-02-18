<?php

namespace Xgbnl\Cloud\Traits;

use Xgbnl\Cloud\Attributes\OAName;
use Xgbnl\Cloud\Proxy\Proxies;

/**
 * @method void  abort(int $code, string $message) 触发一个自定义的异常
 * @method mixed endpoint(mixed $needle, string $domain, bool $replace = false) 为图像添加或移除域名
 */
#[OAName(Proxies::class)]
trait CallMethodCollection
{
    use ReflectionParse;
}