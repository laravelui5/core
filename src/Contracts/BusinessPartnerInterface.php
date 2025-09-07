<?php

namespace LaravelUi5\Core\Contracts;

/**
 * Contract for any domain object representing a business partner —
 * i.e., an individual or organization capable of owning records,
 * receiving settings, or acting within the system.
 *
 * This interface provides the minimal required identity for resolving
 * context, auditing, ownership, and settings-related concerns.
 *
 * It deliberately avoids any dependencies on the Auth package.
 * For role-based access logic, see RoleAwareBusinessPartnerInterface (in Auth).
 */
interface BusinessPartnerInterface
{
    /**
     * Returns the unique internal ID of this business partner.
     *
     * This ID is used for identity resolution, ownership, logging, and
     * contextual lookups throughout the system.
     *
     * @return int
     */
    public function getId(): int;

    /**
     * Returns a human-readable display name for this business partner.
     *
     * This is useful for logs, UI representations, audits, and
     * debugging purposes.
     *
     * @return string
     */
    public function getDisplayName(): string;

    /**
     * Returns the IDs of all teams this business partner is currently a member of.
     *
     * This is primarily used to determine group-level visibility or permission contexts,
     * such as team-level settings overrides.
     *
     * If your system does not support teams, this method may return an empty array.
     *
     * @return int[] List of team IDs the partner belongs to
     */
    public function getTeamIds(): array;
}
