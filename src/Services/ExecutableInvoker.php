<?php

namespace LaravelUi5\Core\Services;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\Http\FormRequest;
use LaravelUi5\Core\Contracts\ExecutableInvokerInterface;
use LaravelUi5\Core\Contracts\ParameterResolverInterface;
use LaravelUi5\Core\Contracts\SettingResolverInterface;
use LaravelUi5\Core\Exceptions\InvalidParameterTypeException;
use LogicException;
use ReflectionMethod;
use ReflectionNamedType;

final readonly class ExecutableInvoker implements ExecutableInvokerInterface
{
    public function __construct(
        private Container                  $container,
        private ParameterResolverInterface $parameterResolver,
        private SettingResolverInterface   $settingResolver,
    )
    {
    }

    /**
     * @throws BindingResolutionException
     */
    public function invoke(object $target, string $method): mixed
    {
        if (!method_exists($target, $method)) {
            throw new LogicException(
                sprintf(
                    'Executable %s must define a %s() method.',
                    $target::class,
                    $method
                )
            );
        }

        // 1. Resolve parameters
        $parameters = $this->parameterResolver->resolve($target);

        // 2. Inject settings
        $this->settingResolver->resolve($target);

        // 3. Build arguments from method signature
        $reflection = new ReflectionMethod($target, $method);
        $arguments = [];

        foreach ($reflection->getParameters() as $argument) {
            $type = $argument->getType();

            if (!$type instanceof ReflectionNamedType) {
                throw new LogicException(
                    'Union or untyped parameters are not supported.'
                );
            }

            $paramClass = $type->getName();

            // FormRequest
            if (is_subclass_of($paramClass, FormRequest::class)) {
                /** @var FormRequest $request */
                $request = $this->container->make($paramClass);

                // Triggers authorize() + validation()
                $request->validateResolved();

                $arguments[$argument->getName()] = $request;
                continue;
            }

            // Declarative parameters
            if (array_key_exists($argument->getName(), $parameters)) {

                $value = $parameters[$argument->getName()];

                // Type guard against method signature
                if (!is_a($value, $paramClass)) {
                    throw new InvalidParameterTypeException($argument->getName());
                }

                $arguments[$argument->getName()] = $value;
                continue;
            }

            // Container-resolvable service
            if ($this->container->has($paramClass)) {
                $arguments[$argument->getName()] = $this->container->make($paramClass);
                continue;
            }

            throw new LogicException(
                sprintf(
                    'Unable to resolve parameter $%s (%s) for %s::%s().',
                    $argument->getName(),
                    $paramClass,
                    $target::class,
                    $method
                )
            );
        }

        // 4. Invoke via container
        return $this->container->call([$target, $method], $arguments);
    }
}
