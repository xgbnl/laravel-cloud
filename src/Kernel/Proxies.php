<?php

declare(strict_types=1);

namespace Xgbnl\Cloud\Kernel;

use Illuminate\Http\JsonResponse;
use InvalidArgumentException;
use Xgbnl\Cloud\Decorates\Factory\DecorateFactory;
use Xgbnl\Cloud\Kernel\Paginator\Paginator;

final readonly class Proxies
{
    public function json(mixed $data = null, int $code = 200): JsonResponse
    {
        $r = ['msg' => null, 'code' => $code];

        if (is_string($data)) {
            $r['msg'] = $data;
        } elseif (!is_null($data)) {
            $r['data'] = $data;
        }

        return new JsonResponse($r);
    }

    public function paginator(array $data): Paginator
    {
        $pageNum = (int)request()->get('pageNum', 1);
        $pageSize = (int)request()->get('pageSize', 10);

        $offset = ($pageNum * $pageSize) - $pageSize;

        $items = array_slice($data, $offset, $pageSize, true);

        $total = count($data);

        return new Paginator($items, $total, $pageSize, $pageNum, [
            'path'     => Paginator::resolveCurrentPath(),
            'pageName' => 'pageNum',
        ]);
    }

    public function abort(int $code, string $message): void
    {
        throw new InvalidArgumentException($message, $code);
    }

     public function endpoint(mixed $needle, string $domain = null, bool $replace = false): array|string
    {
        $decorate = DecorateFactory::builderDecorate($needle);

        return $replace ? $decorate->removeEndpoint($needle, $domain) : $decorate->endpoint($needle, $domain);
    }

    public function tree(array $list, string $id = 'id', string $pid = 'pid', string $son = 'children'): array
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
