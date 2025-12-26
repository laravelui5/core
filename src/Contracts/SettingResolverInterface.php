<?php

namespace LaravelUi5\Core\Contracts;

/**
 * Resolves effective settings for a Configurable target into an immutable Ui5Config.
 *
 * Notes:
 * - Core implementation returns defaults only.
 * - This method MUST be side-effect-free and MUST NOT mutate $target.
 */
interface SettingResolverInterface
{
    /**
     * Build an immutable configuration bag for the given target and context.
     *
     * @param ConfigurableInterface $target The class decorated with #[Setting(...)] attributes
     * @param Ui5ContextInterface|null $ctx Runtime context (artifact, tenant, partner, locale). Can be null in tests.
     * @return Ui5Config Typed, read-only configuration values keyed by setting key
     */
    public function resolve(ConfigurableInterface $target, ?Ui5ContextInterface $ctx = null): Ui5Config;
}
