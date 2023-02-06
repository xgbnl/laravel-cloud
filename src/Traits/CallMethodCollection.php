<?php

namespace Xgbnl\Cloud\Traits;

use Xgbnl\Cloud\Attributes\OAName;
use Xgbnl\Cloud\Utils\CustomMethods;

/**
 * @method array filterFields(array $haystack, string|array $fields, bool $returnOrigin = true) 过滤移除指定字段，returnOrigin为true时返回移除指定字段后的原数组，给定false时返回参数fields的值
 * @method void  trigger(int $code, string $message) 触发一个自定义的异常
 * @method mixed endpoint(mixed $needle, string $domain, bool $replace = false) 为图像添加或移除域名
 * @method array customMerge(array $haystack, array $needle) 自定义合并数组
 * @method string customSubStr(string $haystack, string $symbol, bool $tail = false) 截取字符串开头或结尾的串
 */
#[OAName(CustomMethods::class)]
trait CallMethodCollection
{
    use ReflectionParse;
}