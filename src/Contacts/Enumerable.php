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
     * 利用给定值解析一个枚举实例
     * @param string $value
     * @return Enumerable
     */
    public static function resolve(string $value): Enumerable;

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
}
