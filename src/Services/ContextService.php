<?php

namespace LaravelUi5\Core\Services;

use Illuminate\Support\Str;
use LaravelUi5\Core\Contracts\BusinessPartnerInterface;
use LaravelUi5\Core\Contracts\ContextContributorInterface;
use LaravelUi5\Core\Contracts\ContextServiceInterface;
use LaravelUi5\Core\Contracts\TenantInterface;
use LaravelUi5\Core\Contracts\Ui5Context;
use LaravelUi5\Core\Ui5\Ui5Registry;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Default implementation of the ContextServiceInterface.
 *
 * This class resolves runtime context for a given UI5 app and business partner by
 * invoking a chain of configured ContextContributor classes.
 *
 * The contributors are defined in `config/ui5.php` under the `contexts` key.
 * Each contributor is responsible for a part of the final context (e.g. 'user', 'settings').
 * The result is an associative array, indexed by context parts:
 *
 * Example result:
 * [
 *     'user' => [...],
 *     'settings' => [...],
 *     'abilities' => [...],
 * ]
 *
 * ### Configuration in config/ui5.php
 *
 * 'contexts' => [
 *     'default' => [
 *         'user'      => UserContextContributor::class,
 *         'abilities' => AbilitiesContextContributor::class,
 *         'settings'  => SettingsContextContributor::class,
 *     ],
 *     'offers' => [
 *         'user'      => CustomUserContributor::class,
 *         'settings'  => CustomSettingsContributor::class,
 *     ],
 * ]
 *
 * The key (e.g. 'offers') is derived from the last part of the UI5 app ID (e.g. 'io.pragmatiqu.offers').
 * If no app-specific key is found, the 'default' contributors are used.
 */
class ContextService implements ContextServiceInterface
{
    public function getContext(): array
    {
        $ui5RuntimeContext = app(Ui5Context::class);

        $context = [];

        $config = config('ui5.contexts');

        $appKey = $this->mapUi5NamespaceToContextKey($ui5RuntimeContext->artifact->namespace);

        // Load contributors config
        $appContextConfig = $config[$appKey] ?? $config['default'] ?? [];

        foreach ($appContextConfig as $key => $contributorClass) {
            /** @var ContextContributorInterface $contributor */
            $contributor = app($contributorClass);

            $part = $contributor->contribute($ui5RuntimeContext);

            $context[$key] = $part;
        }

        return $context;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function makeContext(
        string $ui5AppId,
        ?TenantInterface $tenant = null,
        ?BusinessPartnerInterface $partner = null,
        ?BusinessPartnerInterface $authPartner = null,
        ?string $locale = null
    ): Ui5Context
    {
        $ui5Artifact = app(Ui5Registry::class)->get($ui5AppId);
        return new Ui5Context(null, $ui5Artifact, $tenant, $partner, $authPartner, $locale);
    }

    /**
     * Converts a full UI5 app ID to a simplified context config key.
     * Example: 'io.pragmatiqu.offers' → 'offers'
     *
     * @param string $ui5AppId
     * @return string
     */
    protected function mapUi5NamespaceToContextKey(string $ui5AppId): string
    {
        return Str::afterLast($ui5AppId, '.');
    }
}
