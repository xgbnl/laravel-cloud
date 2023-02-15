<?php

namespace Xgbnl\Cloud\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller as BaseController;
use Xgbnl\Cloud\Cache\Cacheable;
use Xgbnl\Cloud\Contacts\Dominator;
use Xgbnl\Cloud\Contacts\Factory;
use Xgbnl\Cloud\Exceptions\FailedResolveException;
use Xgbnl\Cloud\Providers\ControllerProvider;
use Xgbnl\Cloud\Providers\Provider;
use Xgbnl\Cloud\Repositories\Repositories;
use Xgbnl\Cloud\Services\Service;
use Xgbnl\Cloud\Traits\CallMethodCollection;
use Xgbnl\Cloud\Traits\PropertiesTrait;
use Xgbnl\Cloud\Validator\Validator;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property Repositories $repository
 * @property Service $service
 * @property Cacheable $cache
 */
abstract class Controller extends BaseController implements Dominator
{
    use  CallMethodCollection, PropertiesTrait;

    protected ?Request $request = null;

    private readonly Factory|Provider $factory;

    public function __construct()
    {
        $this->factory = new ControllerProvider($this);
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
            return app($this->getModelName())->all();
        }

        return app($this->getModelName())->validated();
    }

    final protected function validator(bool $autoValidate = true): Validator
    {
        $this->prepareRequest();

        return app($this->getModelName(), ['autoValidate' => $autoValidate]);
    }

    final public function refresh(string $abstract = null): static
    {
        $this->assign($abstract);

        return $this;
    }

    private function prepareRequest(): void
    {
        $this->factory->resolveClass('request');

        if (!is_subclass_of($this->getModelName(), FormRequest::class)) {
            throw new FailedResolveException('无法验证表单');
        }
    }
}