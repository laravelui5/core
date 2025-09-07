<?php

namespace LaravelUi5\Core\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use LaravelUi5\Core\Attributes\Parameter;
use LaravelUi5\Core\Contracts\ParameterResolverInterface;
use LaravelUi5\Core\Contracts\Ui5Args;
use LaravelUi5\Core\Enums\ParameterSource;
use LaravelUi5\Core\Enums\ValueType;
use LaravelUi5\Core\Exceptions\InvalidArrayParameterException;
use LaravelUi5\Core\Exceptions\InvalidArrayValueParameterException;
use LaravelUi5\Core\Exceptions\InvalidJsonParameterException;
use LaravelUi5\Core\Exceptions\InvalidParameterDateException;
use LaravelUi5\Core\Exceptions\InvalidParameterException;
use LaravelUi5\Core\Exceptions\InvalidParameterTypeException;
use LaravelUi5\Core\Exceptions\InvalidParameterValueException;
use LaravelUi5\Core\Exceptions\InvalidPathException;
use LaravelUi5\Core\Exceptions\MissingRequiredParameterException;
use LaravelUi5\Core\Exceptions\MissingUriKeyException;
use LaravelUi5\Core\Exceptions\NoModelFoundForParameterException;
use LaravelUi5\Core\Ui5\Contracts\ParameterizableInterface;
use ReflectionClass;
use Throwable;

readonly class ParameterResolver implements ParameterResolverInterface
{

    public function __construct(
        private Request $request
    )
    {
    }

    public function resolve(ParameterizableInterface $target): Ui5Args
    {
        $reflection = new ReflectionClass($target);
        $attributes = $reflection->getAttributes(Parameter::class);
        $uriKeys = $this->getUriKeys();
        $out = [];

        /** @var Parameter $attribute */
        foreach ($attributes as $a) {
            $attribute = $a->newInstance();
            $name = $attribute->name;
            $raw = null;

            // 1. Get the raw value
            switch ($attribute->source) {
                case ParameterSource::Path:
                    if (null === $attribute->uriKey) {
                        throw new MissingUriKeyException($name);
                    }
                    $raw = $uriKeys[$attribute->uriKeyPosition] ?? null;
                    break;
                case ParameterSource::Query:
                    $raw = $this->request->query($name);
                    break;
            }

            // 2. Handle required, default, and nullable
            if (null === $raw) {
                if ($attribute->required && !$attribute->nullable && $attribute->default === null) {
                    throw new MissingRequiredParameterException($name);
                }
                $out[$name] = $attribute->nullable ? null : $attribute->default;
                continue;
            }

            // 3. Cast raw value
            $casted = $this->cast($raw, $attribute->type, $attribute->model, $name);

            // 4. Validate casted value
            if (null === $casted && !$attribute->nullable) {
                throw new InvalidParameterValueException($name, $attribute->type->label());
            }

            $out[$name] = $casted;
        }

        return new Ui5Args($out);
    }

    public function getUriKeys(): array
    {
        $route = $this->request->route();
        $uri = $route?->parameter('uri');
        $segments = is_string($uri) ? explode('/', $uri) : [];
        if (in_array("", $segments, true)) {
            throw new InvalidPathException($uri);
        }
        return $segments;
    }

    private function cast(mixed $value, ValueType $type, ?string $modelClass, string $name): mixed
    {
        switch ($type) {
            case ValueType::Integer:
                return filter_var($value, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);

            case ValueType::Float:
                return filter_var($value, FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE);

            case ValueType::Boolean:
                // Accepts "true/false/1/0/on/off/yes/no"
                return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

            case ValueType::String:
                return is_string($value) ? $value : (string)$value;

            case ValueType::Date:
                try {
                    return Carbon::parse($value);
                } catch (Throwable) {
                    throw new InvalidParameterDateException($name);
                }

            case ValueType::IntegerArray:
                return $this->normalizeArray($value, fn($v) => filter_var($v, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE), $name);

            case ValueType::FloatArray:
                return $this->normalizeArray($value, fn($v) => filter_var($v, FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE), $name);

            case ValueType::BooleanArray:
                return $this->normalizeArray($value, fn($v) => filter_var($v, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE), $name);

            case ValueType::StringArray:
                return $this->normalizeArray($value, fn($v) => (string)$v, $name);

            case ValueType::Model:
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

    /**
     * Normalize input into an array of the given type.
     *
     * @param mixed $value   Raw value (array or JSON string)
     * @param callable $caster Function to cast each element
     * @param string $name   Parameter name (for error reporting)
     * @return array
     */
    private function normalizeArray(mixed $value, callable $caster, string $name): array
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (!is_array($decoded)) {
                throw new InvalidJsonParameterException($name);
            }
            $value = $decoded;
        }

        if (!is_array($value)) {
            throw new InvalidArrayParameterException($name);
        }

        $out = [];
        foreach ($value as $v) {
            $casted = $caster($v);
            if ($casted === null) {
                throw new InvalidArrayValueParameterException($name);
            }
            $out[] = $casted;
        }
        return $out;
    }
}
