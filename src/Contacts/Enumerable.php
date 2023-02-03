<?php

namespace Xgbnl\Cloud\Contacts;

interface Enumerable
{
    /**
     * 转实例转换为数组
     * @return array
     */
    public function convert(): array;

    /**
     * 将所有枚举项转换为数组并返回
     * @return array<array>
     */
    public static function toArray(): array;

    /**
     * 提取所有枚举项的值
     * @return array<string>
     */
    public static function values(): array;

    public static function implode(): string;
}
