<?php

namespace Xgbnl\Cloud\Traits;

use Xgbnl\Cloud\Contacts\Enum\Enumerable;

trait EnumerableTrait
{

    final public const FILTER_RETURNS_CONVERT = 0;

    final public const FILTER_RETURNS_INSTANCE = 1;

    final public const FILTER_RETURNS_VALUES = 2;

    public static function values(): array
    {
        return array_map(fn($item) => $item->value, self::cases());
    }

    /**
     * 为当前枚举项设定格式化的中文展示名称,该方法和convert方法进行配合
     * @param string $label
     * @return array
     */
    protected function format(string $label): array
    {
        return [
            'label' => $label,
            'value' => $this->value,
        ];
    }

    public function label(): string
    {
        return $this->convert()['label'];
    }

    public static function toArray(): array
    {
        return array_map(fn($enum) => $enum->convert(), self::cases());
    }

    public static function join(string $haystack = ','): string
    {
        return implode($haystack, self::values());
    }

    protected static function filter(\Closure $closure, int $returns = self::FILTER_RETURNS_CONVERT): array
    {
        return array_reduce(self::cases(), function (mixed $carry, Enumerable $enum) use ($closure, $returns) {

            if ($closure($enum)) {
                $carry[] = match ($returns) {
                    self::FILTER_RETURNS_CONVERT  => $enum->convert(),
                    self::FILTER_RETURNS_INSTANCE => $enum,
                    self::FILTER_RETURNS_VALUES   => $enum->value,
                };
            }

            return $carry;
        }, []);
    }
}