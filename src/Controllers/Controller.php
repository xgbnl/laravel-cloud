<?php

namespace Xgbnl\Cloud\Controllers;

use App\Http\Controllers\Controller as BaseController;
use Illuminate\Http\Request;
use Xgbnl\Cloud\Cache\Cacheable;
use Xgbnl\Cloud\Contacts\Contextual;
use Xgbnl\Cloud\Exceptions\FailedResolveException;
use Xgbnl\Cloud\Kernel\Proxies\Contacts\Factory;
use Xgbnl\Cloud\Kernel\Proxies\ControllerProxy;
use Xgbnl\Cloud\Proxy\Paginator\Paginator;
use Xgbnl\Cloud\Repositories\Repositories;
use Xgbnl\Cloud\Services\Service;
use Xgbnl\Cloud\Traits\ContextualTrait;
use Xgbnl\Cloud\Validator\Validator;

/**
 * @property Repositories $repository
 * @property Service $service
 * @property Cacheable $cache
 * @method JsonResponse json(mixed $data = null, int $code = 200)
 * @method Paginator paginator(array $data)
 */
abstract class Controller extends BaseController implements Contextual
{
    use ContextualTrait;

    protected ?Request $request = null;

    private readonly Factory $factory;

    public function __construct(ControllerProxy $factory)
    {
        $this->factory = $factory;
    }

    final protected function validatedForm(array $extras = []): array
    {
        $this->prepareRequest();

        if (!empty($extras)) {
            $this->request->merge($extras);
            return app($this->factory->getAccessor())->all();
        }

        return app($this->factory->getAccessor())->validated();
    }

    final protected function validator(bool $autoValidate = true): Validator
    {
        $this->prepareRequest();

        return app($this->factory->getAccessor(), ['autoValidate' => $autoValidate]);
    }

    private function prepareRequest(): void
    {
        $this->factory->getModel($this->getAlias(), 'request');

        if (!is_subclass_of($this->factory->getAccessor(), Validator::class)) {
            throw new FailedResolveException('无法验证表单');
        }
    }

    final protected function refresh(string $abstract = null): static
    {
        $this->factory->refresh($abstract);

        return $this;
    }

    public function callAction($method, $parameters)
    {
        $injected = false;

        foreach ($parameters as $p) {
            if ($p instanceof Request) {
                $this->request = $p;
                $injected      = true;
            }
        }

        if (!$injected) {
            $this->request = \request();
        }

        return parent::callAction($method, $parameters);
    }

    public function __call(string $method, array $parameters)
    {
        return $this->factory->app()->callAction($this, $method, $parameters);
    }
}