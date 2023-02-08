<?php

declare(strict_types=1);

namespace Xgbnl\Cloud\Utils;

use Illuminate\Http\JsonResponse;
use InvalidArgumentException;
use Xgbnl\Cloud\Decorates\Factory\DecorateFactory;
use Xgbnl\Cloud\Paginator\Paginator;

readonly final class CustomMethods
{
    /**
     * Custom return json
     * @param mixed|null $data
     * @param int $code
     * @return JsonResponse
     */
    static public function json(mixed $data = null, int $code = 200): JsonResponse
    {
        $r = ['msg' => null, 'code' => $code];

        if (is_string($data)) {
            $r['msg'] = $data;
        } elseif (!is_null($data)) {
            $r['data'] = $data;
        }

        return new JsonResponse($r);
    }

    /**
     * Custom paginate.
     * @param array $list
     * @param bool $isPaginate
     * @return Paginator
     */
    static public function customPaginate(array $list = [], bool $isPaginate = true): Paginator
    {
        $pageNum = (int)request()->get('pageNum', 1);
        $pageSize = (int)request()->get('pageSize', 10);

        $offset = ($pageNum * $pageSize) - $pageSize;

        $items = $isPaginate ? array_slice($list, $offset, $pageSize, true) : $list;

        $total = count($list);

        return new Paginator($items, $total, $pageSize, $pageNum, [
            'path'     => Paginator::resolveCurrentPath(),
            'pageName' => 'pageNum',
        ]);
    }

    /**
     * 触发一个自定义的异常
     * @param int $code
     * @param string $message
     * @return void
     */
    static public function abort(int $code, string $message): void
    {
        throw new InvalidArgumentException($message, $code);
    }

    /**
     * 为图片
     * @param mixed $needle
     * @param string|null $domain
     * @param bool $replace
     * @return array|string
     */
    static public function endpoint(mixed $needle, string $domain = null, bool $replace = false): array|string
    {
        $decorate = DecorateFactory::builderDecorate($needle);

        return $replace ? $decorate->removeEndpoint($needle, $domain) : $decorate->endpoint($needle, $domain);
    }

    /**
     * 生成树结构
     * @param array $list
     * @param string $id
     * @param string $pid
     * @param string $son
     * @return array
     */
    static public function tree(array $list, string $id = 'id', string $pid = 'pid', string $son = 'children'): array
    {
        list($tree, $map) = [[], []];
        foreach ($list as $item) {
            $map[$item[$id]] = $item;
        }

        foreach ($list as $item) {
            (isset($item[$pid]) && isset($map[$item[$pid]]))
                ? $map[$item[$pid]][$son][] = &$map[$item[$id]]
                : $tree[] = &$map[$item[$id]];
        }

        unset($map);
        return $tree;
    }
}
