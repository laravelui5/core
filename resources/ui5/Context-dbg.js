sap.ui.define([
	"sap/ui/core/Control"
], function (Control) {
	"use strict";

	return Control.extend("com.laravelui5.core.Context", {

		metadata: {
			properties: {
				uuid: {
					type: "string"
				},
				showIndicator: {
					type: "boolean",
					defaultValue: true
				}
			},
			aggregations: {
				content: {
					type: "sap.ui.core.Control",
					multiple: true
				}
			},
			defaultAggregation: "content"
		},


		renderer: {
			apiVersion: 2,
			render: function (rm, control) {
				// Wichtig: Ein echtes DOM-Element erzeugen!
				rm.openStart("help-context", control);
				rm.attr("data-help-uuid", control.getUuid());
				rm.openEnd();

				// slot: alle Kinder durchreichen
				let children = control.getContent() || [];
				for (let i = 0; i < children.length; i++) {
					rm.renderControl(children[i]);
				}

				if (control.getShowIndicator && control.getShowIndicator()) {

					rm.openStart("div");
					rm.class("help-indicator");
					rm.attr("data-help-uuid", control.getUuid());
					rm.attr("data-help-trigger", "");
					rm.openEnd();

					// LuxIcon (help)
					rm.openStart("lux-icon");
					rm.attr("name", "help");
					rm.openEnd();
					rm.close("lux-icon");

					rm.close("div");
				}

				rm.close("help-context");
			}
		}
	});
});
