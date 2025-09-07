<?php

namespace LaravelUi5\Core\Contracts;

/**
 * Defines a contract for authorization logic within the UI5 runtime context.
 *
 * This interface allows application components (such as Tiles, Cards, Dashboards)
 * to delegate permission checks to a centralized service.
 *
 * The implementation should evaluate whether a given ability is permitted
 * in the context of the currently active business partner, user session,
 * or any other relevant runtime scope provided via the Ui5RuntimeContext.
 *
 * Implementations can connect to roles, permissions, policies, or external ACL systems.
 *
 * Example usage:
 *  $authService->authorize('tile.view.pending', $context);
 */
interface AuthServiceInterface
{
    /**
     * Checks whether the given ability is authorized for the current UI5 runtime context.
     *
     * This method encapsulates all permission logic and may consider roles,
     * business partner relationships, tenant, environment, or other runtime factors.
     *
     * @param string $ability A string representing the named ability to check (e.g. 'tile.view.offers')
     * @param Ui5Context $context The contextual information for the current request
     * @return bool True if access is granted, false otherwise
     */
    public function authorize(string $ability, Ui5Context $context): bool;
}
