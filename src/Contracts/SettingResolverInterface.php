<?php

namespace LaravelUi5\Core\Contracts;

use LaravelUi5\Core\Ui5\Contracts\ConfigurableInterface;

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
     * @param Ui5Context|null $ctx Runtime context (artifact, tenant, partner, locale). Can be null in tests.
     * @return Ui5Config Typed, read-only configuration values keyed by setting key
     */
    public function resolve(ConfigurableInterface $target, ?Ui5Context $ctx = null): Ui5Config;
}
