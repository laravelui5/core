sap.ui.define([
	"sap/ui/core/UIComponent",
	"sap/ui/core/mvc/XMLView",
	"sap/m/Dialog"
], function (UIComponent, XMLView, Dialog) {
	"use strict";

	/**
	 * BaseComponent
	 *
	 * Base component for all LaravelUi5 applications.
	 *
	 * Supports two startup modes:
	 *  - default app mode (router-based)
	 *  - dialog mode (router-less, view-only)
	 *
	 * Dialog mode is activated via componentData:
	 * {
	 *   mode: "dialog",
	 *   dialogView: "fully.qualified.view.Name"
	 * }
	 */
	return UIComponent.extend("com.laravelui5.core.BaseComponent", {

		/**
		 * Opens a global dialog view within the ownership context of this UI5 component.
		 *
		 * The dialog view must follow these conventions:
		 *
		 * - It MUST be an `sap.ui.core.mvc.View`.
		 * - The view MUST have exactly one root control.
		 * - The root control MUST be a `sap.m.SelectDialog`
		 *
		 * The dialog is opened ephemerally:
		 * - It is created on demand.
		 * - It is opened immediately.
		 * - It is fully destroyed after it is closed.
		 *
		 * Optional lifecycle hook:
		 * - If the dialog's controller implements `initDialog(dialog)`,
		 *   this method will be invoked once after the dialog has been opened.
		 *
		 * The dialog runs in the ownership context of this component,
		 * ensuring correct access to models, i18n, and other component-scoped resources.
		 *
		 * @param {string} viewName
		 *   Fully qualified UI5 view name of the dialog view.
		 *
		 * @returns {Promise<sap.ui.core.Control>}
		 *   Resolves once the dialog has been created and opened.
		 *   The resolved value is the dialog control instance.
		 *
		 * @throws {Error}
		 *   If the view structure or root control does not match the expected dialog conventions.
		 */
		openGlobalDialog: function (viewName) {
			const that = this;

			return this.runAsOwner(function () {
				return XMLView.create({
					viewName: viewName,
					async: true
				}).then(function (view) {

					const content = view.getContent?.();

					if (!content || 1 !== content.length) {
						throw new Error(
							"Global dialog view must have exactly one root control"
						);
					}

					const dialog = content[0];

					if (!(dialog instanceof Dialog)) {
						throw new Error(
							"Global dialog view root control must be an instance of sap.m.Dialog"
						);
					}

					dialog.attachAfterClose(function () {
						dialog.destroy();
						view.destroy();
					});

					const controller = view.getController?.();

					if (controller && typeof controller.initDialog === "function") {
						dialog.attachAfterOpen(() => {
							controller.initDialog(dialog);
						});
					}

					return dialog.open();
				});
			});
		},
	});
});
