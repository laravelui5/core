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
     * Resolve and inject all declared settings into the target.
     *
     * @param object $target
     */
    public function resolve(object $target): void;
}
