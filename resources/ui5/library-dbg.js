/*!
 * ${copyright}
 */

/**
 * Initialization Code and shared classes of library io.pragmatiqu.tools
 */
sap.ui.define([
	"sap/base/util/ObjectPath",
	"sap/ui/core/library"
], function (ObjectPath) {
	"use strict";

	// delegate further initialization of this library to the Core
	// Hint: sap.ui.getCore() must still be used to support preload with sync bootstrap!
	sap.ui.getCore().initLibrary({
		name: "io.pragmatiqu.core",
		version: "1.0.0",
		dependencies: [
			// keep in sync with the ui5.yaml and .library files
			"sap.ui.core"
		],
		types: [],
		interfaces: [],
		controls: [],
		elements: [
			"io.pragmatiqu.core.LaravelUi5",
			"io.pragmatiqu.core.Connection",
			"io.pragmatiqu.core.ContextProvider",
			"io.pragmatiqu.core.BaseController"
		],
		noLibraryCSS: true // if no CSS is provided, you can disable the library.css load here
	});


	/**
	 * Some description about <code>io.pragmatiqu.core</code>
	 *
	 * @namespace
	 * @alias io.pragmatiqu.core
	 * @author Michael Gerzabek
	 * @version 1.0.0
	 * @public
	 */
	return ObjectPath.get("io.pragmatiqu.core");
});
