<?php

namespace Xgbnl\Cloud\Kernel;

final readonly class Str
{
    public function split(string $haystack, string|array $needle): string
    {
        if (is_string($needle)) {
            return str_ends_with($haystack, $needle) ? $this->customSubStr($haystack, $needle) : $haystack;
        }

        if (is_array($needle)) {
            $needle = array_filter($needle, fn(string $ends) => str_ends_with($haystack, $ends));
            $needle = array_pop($needle);

            return $this->customSubStr($haystack, $needle);
        }

        return $haystack;
    }

    protected function customSubStr(string $haystack, string $needle): string
    {
        return substr($haystack, 0, -strlen($needle));
    }

    public function explode(string $class): array
    {
        $splice = explode('\\', $class);

        return ['namespace' => reset($splice), 'class' => end($splice)];
    }
}