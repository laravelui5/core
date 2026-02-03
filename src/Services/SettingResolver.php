<?php

namespace LaravelUi5\Core\Services;

use LaravelUi5\Core\Attributes\Setting;
use LaravelUi5\Core\Contracts\SettingResolverInterface;
use LaravelUi5\Core\Exceptions\InvalidSettingException;
use LaravelUi5\Core\Ui5\AbstractConfigurable;
use ReflectionClass;

readonly class SettingResolver implements SettingResolverInterface
{
    public function resolve(object $target): void
    {
        if (!$target instanceof AbstractConfigurable) {
            throw new InvalidSettingException(
                sprintf(
                    'Settings can only be injected into subclasses of %s, %s given.',
                    AbstractConfigurable::class,
                    $target::class
                )
            );
        }

        $reflection = new ReflectionClass($target);
        $attributes = $reflection->getAttributes(Setting::class);

        if ($attributes === []) {
            // Nothing to inject – but still valid
            return;
        }

        $resolved = [];

        foreach ($attributes as $attribute) {
            /** @var Setting $definition */
            $definition = $attribute->newInstance();

            if (array_key_exists($definition->key, $resolved)) {
                throw new InvalidSettingException(
                    sprintf(
                        'Duplicate setting "%s" declared on %s.',
                        $definition->key,
                        $target::class
                    )
                );
            }

            // Core does not cast defaults.
            $resolved[$definition->key] = $definition->default;
        }

        $target->injectSettings($resolved);
    }
}
