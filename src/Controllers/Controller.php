<?php

namespace Xgbnl\Cloud\Controllers;

use App\Http\Controllers\Controller as BaseController;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Xgbnl\Cloud\Cache\Cacheable;
use Xgbnl\Cloud\Contacts\Contextual;
use Xgbnl\Cloud\Exceptions\FailedResolveException;
use Xgbnl\Cloud\Kernel\Proxies\Contacts\Factory;
use Xgbnl\Cloud\Kernel\Proxies\ControllerProxy;
use Xgbnl\Cloud\Repositories\Repositories;
use Xgbnl\Cloud\Services\Service;
use Xgbnl\Cloud\Traits\CallMethodCollection;
use Xgbnl\Cloud\Traits\ContextualTrait;
use Xgbnl\Cloud\Validator\Validator;

/**
 * @property Repositories $repository
 * @property Service $service
 * @property Cacheable $cache
 */
abstract class Controller extends BaseController implements Contextual
{
    use  CallMethodCollection, ContextualTrait;

    protected ?Request $request = null;

    private readonly Factory $factory;

    public function __construct(ControllerProxy $factory)
    {
        $this->factory = $factory;
    }

    public function callAction($method, $parameters)
    {
        $injected = false;

        foreach ($parameters as $p) {
            if ($p instanceof Request) {
                $this->request = $p;
                $injected = true;
            }
        }

        if (!$injected) {
            $this->request = \request();
        }

        return parent::callAction($method, $parameters);
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

    final protected function refresh(string $abstract = null): static
    {
        $this->factory->refresh($abstract);

        return $this;
    }

    private function prepareRequest(): void
    {
        $this->factory->getModel($this->getAlias(), 'request');

        if (!is_subclass_of($this->factory->getAccessor(), FormRequest::class)) {
            throw new FailedResolveException('无法验证表单');
        }
    }
}