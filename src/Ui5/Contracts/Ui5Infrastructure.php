<?php

namespace LaravelUi5\Core\Ui5\Contracts;

/**
 * Marker interface for UI5 Infrastructure Modules.
 *
 * ---
 *
 * **Infrastructure Module**
 *
 * An Infrastructure Module is a fully capable UI5 module that is
 * implicitly present as part of the platform itself.
 *
 * It provides technical or cross-cutting functionality (e.g. authentication,
 * dashboards, reporting, diagnostics, help) and may expose full UI5
 * applications, navigation targets, actions, and artifacts.
 *
 * Infrastructure modules:
 * - are automatically registered by their Service Providers
 * - do NOT require explicit declaration in `ui5.modules`
 * - are addressable like any other UI5 app
 * - do NOT represent a product or business decision
 *
 * **Rule of thumb:**
 * Infrastructure modules exist because the platform requires them,
 * not because a product explicitly chose them.
 *
 * ---
 *
 * **Business Module**
 *
 * A Business Module is a UI5 module that represents product functionality
 * and user-facing features.
 *
 * Even though it may be technically complete and fully functional,
 * its visibility is a conscious product decision.
 *
 * Business modules:
 * - MUST be explicitly declared in `ui5.modules`
 * - define what is visible, supported, and offered to users
 * - represent a deliberate product and UX commitment
 *
 * **Rule of thumb:**
 * Visibility is a product decision, not a technical consequence.
 *
 * ---
 *
 * This interface has no methods by design.
 * Implementing it is an explicit declaration of intent.
 */
interface Ui5Infrastructure extends Ui5ModuleInterface
{
}
