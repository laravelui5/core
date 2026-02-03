<?php

namespace LaravelUi5\Core\Ui5;

use LogicException;

/**
 * Base class for all configurable UI5 handlers and providers.
 *
 * Provides read-only, virtual properties for settings declared
 * via #[Setting] attributes.
 *
 * Settings are injected once by the framework before execution
 * and must not be mutated afterwards.
 */
abstract class AbstractConfigurable
{
    /** @var array<string, mixed> */
    private array $__settings = [];

    /**
     * Inject resolved settings.
     *
     * This method is intended to be called exactly once by the framework.
     *
     * @param array<string, mixed> $values
     */
    final public function injectSettings(array $values): void
    {
        if (!empty($this->__settings)) {
            throw new LogicException(
                sprintf(
                    'Settings already injected on %s.',
                    static::class
                )
            );
        }

        foreach ($values as $key => $value) {
            // Prevent collision with real properties
            if (property_exists($this, $key)) {
                throw new LogicException(
                    sprintf(
                        'Cannot inject setting "%s" on %s: property already exists.',
                        $key,
                        static::class
                    )
                );
            }

            $this->__settings[$key] = $value;
        }
    }

    /**
     * Read-only access to injected settings.
     *
     * @param string $name
     * @return mixed
     */
    final public function __get(string $name): mixed
    {
        if (array_key_exists($name, $this->__settings)) {
            return $this->__settings[$name];
        }

        throw new LogicException(
            sprintf(
                'Undefined setting "%s" accessed on %s.',
                $name,
                static::class
            )
        );
    }
}
