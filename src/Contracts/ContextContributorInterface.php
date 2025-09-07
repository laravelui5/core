<?php

namespace LaravelUi5\Core\Contracts;

/**
 * Defines a component responsible for contributing a single part of the context.
 *
 * Each contributor receives the current app and business partner and returns
 * an associative array. These parts will be merged into the final context.
 *
 * Typical examples of contributors include:
 * - UserContextContributor → user info
 * - SettingsContextContributor → resolved settings
 * - AbilitiesContextContributor → permissions & roles
 */
interface ContextContributorInterface
{
    /**
     * Returns a partial context structure based on the current runtime request.
     * This method must return an array or throw an exception.
     *
     * @param Ui5Context $context
     * @return array The partial context this contributor is responsible for
     */
    public function contribute(Ui5Context $context): array;
}
