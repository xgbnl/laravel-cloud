<?php

declare(strict_types=1);

namespace Xgbnl\Cloud\Controllers;

use Illuminate\Http\JsonResponse;
use Xgbnl\Cloud\Kernel\Paginator\Paginator;

/**
 * @method JsonResponse json(mixed $data = null, int $code = 200) 自定义Json返回
 * @method Paginator customPaginate(array $list = [], bool $isPaginate = true) 自定义分页
 */
abstract class CrudController extends Controller
{
    public function store(): JsonResponse
    {
        $validated = $this->validatedForm();

        $this->service->createOrUpdate($validated);

        return $this->json('创建成功', 201);
    }

    public function update(): JsonResponse
    {
        return $this->store();
    }

    public function destroy(): JsonResponse
    {
        $this->service->destroy($this->request->input('id'));

        return $this->json('删除成功', 204);
    }
}
