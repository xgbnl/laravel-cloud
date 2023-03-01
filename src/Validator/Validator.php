<?php

namespace Xgbnl\Cloud\Validator;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Factory;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator as ValidatorContact;

abstract class Validator extends FormRequest
{
    protected array $scenes = [];

    private array   $extendRules  = [];
    private ?string $currentScene = null;

    protected readonly bool $autoValidate;

    public function __construct(array $query = [], array $request = [], array $attributes = [], array $cookies = [], array $files = [], array $server = [], $content = null, bool $autoValidate = true)
    {
        $this->autoValidate = $autoValidate;

        parent::__construct($query, $request, $attributes, $cookies, $files, $server, $content);
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    final public function scene(string $scene): static
    {
        $this->currentScene = $scene;

        return $this;
    }

    final public function with(array|string $rule): static
    {
        if (is_array($rule)) {
            $this->extendRules = array_merge(
                $this->extendRules[],
                array_map(fn($value) => Str::camel($value), $rule)
            );
        } else if (is_string($rule)) {
            $this->extendRules[] = Str::camel($rule);
        }

        return $this;
    }

    final public function validateForm(array $extras = []): array
    {
        $validatedData = $this->resolveValidator()->validated();

        return empty($extra) ? $validatedData : array_merge($validatedData, $extra);
    }

    final public function validateResolved(): void
    {
        if ($this->autoValidate) {
            $this->resolveValidator();
        }
    }

    private function resolveValidator(): ValidatorContact
    {
        if (!$this->passesAuthorization()) {
            $this->failedAuthorization();
        }

        $instance = $this->getValidatorInstance();
        if ($instance->fails()) {
            $this->failedValidation($instance);
        }

        return $instance;
    }

    final public function validator(Factory $factory): \Illuminate\Validation\Validator
    {
        return $factory->make($this->validationData(), $this->prepareRules(), $this->messages(), $this->attributes());
    }

    private function prepareRules(): array
    {
        $rules = $this->container->call([$this, 'rules']);

        if (!empty($this->extendRules)) {
            $extendRules = array_reverse($this->extendRules);
            foreach ($extendRules as $extendRule) {
                if (method_exists($this, "{$extendRule}Rules")) {
                    $rules = array_merge($rules, $this->container->call([$this, "{$extendRule}Rules"]));
                }
            }
        }

        if ($this->currentScene && isset($this->scenes[$this->currentScene])) {
            $sceneFields = is_array($this->scenes[$this->currentScene])
                ? $this->scenes[$this->currentScene]
                : explode(',', $this->scenes[$this->currentScene]);

            return array_reduce($sceneFields, function (mixed $carry, $field) use ($rules) {
                if (array_key_exists($field, $rules)) {
                    $carry[$field] = $rules[$field];
                }

                return $carry;
            }, []);
        }

        return $rules;
    }
}