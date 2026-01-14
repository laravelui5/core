sap.ui.define([
	"com/laravelui5/core/Connection"
], function (Connection) {
	"use strict";

	/** @private */
	let component = null;

	/** @private */
	let connection = null;

	/** @private */
	let settings = {};

	/** @private */
	let shell = null;

	/** @private */
	let resolveReady;

	/**
	 * Core resolves ready() immediately, because no async work exists yet.
	 * SDK will override ready() later with its own promise.
	 *
	 * @private
	 */
	const readyPromise = new Promise(res => {
		resolveReady = res;
	});

	const LaravelUi5 = {

		/**
		 * Initializes the Core facade with the UI5 Component.
		 *
		 * @param {sap.ui.core.UIComponent} ui5Component
		 * @returns {Promise<void>}
		 */
		init(ui5Component) {
			component = ui5Component;

			const uri = component.getManifestEntry("/sap.app/dataSources/mainService/uri");

			connection = new Connection(uri);

			settings = component.getManifestEntry("/laravel.ui5/settings") || {};

			// Core is immediately ready
			resolveReady();

			return Promise.resolve();
		},

		/**
		 * Returns a promise that resolves when the facade is ready.
		 * In Core: resolves immediately.
		 * In SDK: overridden with real async readiness.
		 *
		 * @returns {Promise<void>}
		 */
		ready() {
			return readyPromise;
		},


		// ---------------------------------------------------------------------
		// Authorization / Abilities
		// ---------------------------------------------------------------------

		/**
		 * Ability check placeholder.
		 * Core: always `true`
		 *
		 * @param {string} ability
		 * @returns {boolean}
		 */
		can(ability) {
			return shell ? shell.can(ability) : true;
		},

		/**
		 * View-level authorization check.
		 * Core: always `true`
		 *
		 * @param {string} viewKey
		 * @returns {boolean}
		 */
		authorize(viewKey) {
			return shell ? shell.authorize(viewKey) : true;
		},


		// ---------------------------------------------------------------------
		// Business Partner Context (Core defaults)
		// ---------------------------------------------------------------------

		/**
		 * Acting business partner (Core: null)
		 *
		 * @returns {any|null}
		 */
		getPartner() {
			return shell ? shell.getPartner() : null;
		},

		/**
		 * Authenticated business partner (Core: null)
		 *
		 * @returns {any|null}
		 */
		getAuthenticatedPartner() {
			return shell ? shell.getAuthenticatedPartner() : null;
		},

		/**
		 * Partner representations (Core: empty list)
		 *
		 * @returns {Array}
		 */
		getRepresentations() {
			return shell ? shell.getRepresentations() : [];
		},


		// ---------------------------------------------------------------------
		// Settings (Core defaults)
		// ---------------------------------------------------------------------

		/**
		 * Returns all resolved settings.
		 * Core: empty object
		 *
		 * @returns {Object<string, any>}
		 */
		settings() {
			return shell ? shell.settings() : settings;
		},

		/**
		 * Returns a single resolved setting.
		 * Core: undefined
		 *
		 * @param {string} key
		 * @returns {any | undefined}
		 */
		getSetting(key) {
			return shell ? shell.getSetting(key) : settings[key];
		},


		// ---------------------------------------------------------------------
		// Tenant / Client (Core default)
		// ---------------------------------------------------------------------

		/**
		 * Returns tenant/client information.
		 * Core: null
		 *
		 * @returns {any|null}
		 */
		getClient() {
			return shell ? shell.getClient() : null;
		},


		// ---------------------------------------------------------------------
		// Help System
		// ---------------------------------------------------------------------

		/**
		 * Opens contextual help (Core no-op).
		 *
		 * @param {string} uuid
		 * @param locale
		 */
		showHelp(uuid, locale = null) {
			shell?.showHelp(uuid, locale);
		},

		// ---------------------------------------------------------------------
		// Context Change Listener
		// ---------------------------------------------------------------------

		/**
		 * Attaches a handler for a specific Shell event.
		 *
		 * Shell events provide a stable, named mechanism for reacting to
		 * context changes, representation switches, setting updates, and
		 * other runtime-level signals.
		 *
		 * Example:
		 *   shell.attach("context:changed", handler);
		 *
		 * Event names are part of the public SDK contract and SHOULD be
		 * referenced via constants to ensure long-term stability.
		 *
		 * @param event   Name of the event to listen for (e.g. "context:changed").
		 * @param handler Callback invoked when the event is emitted.
		 */
		attach(event, handler) {
			shell?.attach(event, handler);
		},

		/**
		 * Detaches a previously registered event handler.
		 *
		 * This is the counterpart to `attach` and MUST remove the exact same
		 * handler reference that was provided earlier. If the handler was not
		 * registered or has already been removed, this method silently does nothing.
		 *
		 * Example:
		 *   shell.detach("context:changed", handler);
		 *
		 * @param event   Event name previously passed to `attach`.
		 * @param handler Same handler function originally registered.
		 */
		detach(event, handler) {
			shell?.detach(event, handler);
		},

		// ---------------------------------------------------------------------
		// Telemetry
		// ---------------------------------------------------------------------

		/**
		 * Logs telemetry / diagnostics.
		 * Core: logs to console.
		 *
		 * @param {string} event
		 * @param {any} payload
		 * @param {string} level "debug" | "info" | "warn" | "error"
		 */
		log(event, payload, level) {
			shell ? shell.log(event, payload, level) : console.log("[LaravelUi5/Core] log: " + event, payload);
		},


		// ---------------------------------------------------------------------
		// Backend Access (Connection Wrapper)
		// ---------------------------------------------------------------------

		/**
		 * Calls a manifest-defined backend action.
		 *
		 * @param {string} name - Action key in sap.ui5.actions
		 * @param {object} [params={}] - URL placeholder values
		 * @param {object|null} [body=null] - Optional JSON payload
		 * @returns {Promise<any>} Result from backend
		 */
		async call(name, params = {}, body = null) {
			if (!component) throw new Error("[LaravelUi5] Not initialized.");

			const actions = component.getManifestEntry("/laravel.ui5/actions") || {};

			const action = actions[name];

			if (!action) throw new Error(`[LaravelUi5] Unknown action '${name}'`);

			const url = action.url.replace(/{(\w+)}/g, (_, k) => {
				if (!(k in params)) throw new Error(`[LaravelUi5] Missing param '${k}'`);
				return encodeURIComponent(params[k]);
			});

			const method = (action.method || "POST").toUpperCase();

			const fn = `fetch${method.charAt(0)}${method.slice(1).toLowerCase()}`;

			if (typeof connection[fn] !== "function") {
				throw new Error(`[LaravelUi5] Unsupported HTTP method '${method}'`);
			}

			return await connection[fn](url, body);
		},

		/**
		 * Backend GET
		 */
		get(path) {
			return connection.fetchGet(path);
		},

		/**
		 * Backend POST
		 */
		post(path, data) {
			return connection.fetchPost(path, data);
		},

		/**
		 * Backend PUT
		 */
		put(path, data) {
			return connection.fetchPut(path, data);
		},

		/**
		 * Backend PATCH
		 */
		patch(path, data) {
			return connection.fetchPatch(path, data);
		},

		/**
		 * Backend DELETE
		 */
		delete(path, data) {
			return connection.fetchDelete(path, data);
		},

		/**
		 * Fetches XML from the backend.
		 */
		fetchXml(path) {
			return connection.fetchXml(path);
		},

		fetchHtml(path) {
			return connection.fetchHtml(path);
		},

		/**
		 * Fetches OData EntitySet from the service.
		 */
		fetchEntitySet(path) {
			return connection.fetchEntitySet(path);
		},

		/**
		 * Returns the full OData service URL.
		 */
		getMainServiceUri() {
			return connection.getMainServiceUri();
		},

		/**
		 * Returns the base URL (protocol + host).
		 */
		getBaseUrl() {
			return connection.getBaseUrl();
		}
	};

	/**
	 * INTERNAL: Augmentation hook for SDK / LeanShell.
	 * Not part of the public API contract.
	 *
	 * @internal
	 */
	LaravelUi5.__extend = (componentOverride, connectionOverride, shellOverride) => {
		if (componentOverride) {
			component = componentOverride;
		}
		if (connectionOverride) {
			connection = connectionOverride;
		}
		if (shellOverride) {
			shell = shellOverride;
		}

		resolveReady();
	};

	return LaravelUi5;
});
