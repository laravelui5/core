<?php

namespace LaravelUi5\Core\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use LaravelUi5\Core\Attributes\Parameter;
use LaravelUi5\Core\Contracts\ParameterResolverInterface;
use LaravelUi5\Core\Enums\ParameterType;
use LaravelUi5\Core\Exceptions\InvalidParameterDateException;
use LaravelUi5\Core\Exceptions\InvalidParameterException;
use LaravelUi5\Core\Exceptions\InvalidParameterTypeException;
use LaravelUi5\Core\Exceptions\InvalidParameterValueException;
use LaravelUi5\Core\Exceptions\InvalidPathException;
use LaravelUi5\Core\Exceptions\NoModelFoundForParameterException;
use ReflectionClass;
use Throwable;

readonly class ParameterResolver implements ParameterResolverInterface
{

    public function __construct(private Request $request)
    {
    }

    public function resolve(object $target): array
    {
        $params = [];

        $route = $this->request->route();

        if (!$route) {
            throw new InvalidPathException('No route bound to request.');
        }

        $uri = $route->parameter('uri');
        $segments = is_string($uri) ? explode('/', $uri) : [];

        $reflection = new ReflectionClass($target);
        $attributes = $reflection->getAttributes(Parameter::class);

        // Reject empty segments (/a//b)
        if (in_array('', $segments, true)) {
            throw new InvalidPathException($uri);
        }

        // Reject mismatch route <> definitions
        if (count($segments) !== count($attributes)) {
            throw new InvalidPathException(sprintf(
                'Expected %d path parameters, got %d.',
                count($attributes),
                count($segments)
            ));
        }

        foreach ($attributes as $index => $attribute) {

            /** @var Parameter $definition */
            $definition = $attribute->newInstance();

            $raw = $segments[$index];

            $value = $this->cast(
                $raw,
                $definition->type,
                $definition->model,
                $definition->name
            );

            if ($value === null) {
                throw new InvalidParameterValueException(
                    $definition->name,
                    $definition->type->label()
                );
            }

            $params[$definition->name] = $value;
        }
        return $params;
    }

    private function cast(mixed $value, ParameterType $type, ?string $modelClass, string $name): mixed
    {
        switch ($type) {
            case ParameterType::Integer:
                return filter_var($value, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);

            case ParameterType::Float:
                return filter_var($value, FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE);

            case ParameterType::Boolean:
                // Accepts "true/false/1/0/on/off/yes/no"
                return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

            case ParameterType::String:
                return is_string($value) ? $value : (string)$value;

            case ParameterType::Date:
                try {
                    return Carbon::parse($value);
                } catch (Throwable) {
                    throw new InvalidParameterDateException($name);
                }

            case ParameterType::Model:
                if ($value instanceof $modelClass) {
                    return $value;
                }
                /** @var Model $modelClass */
                if ($modelClass) {
                    $model = $modelClass::find($value);
                    if ($model) {
                        return $model;
                    }
                    throw new NoModelFoundForParameterException($name, $modelClass);
                }
                throw new InvalidParameterException($name);
        }

        throw new InvalidParameterTypeException($name);
    }
}
