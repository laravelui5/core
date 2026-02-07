/*!
 * ${copyright}
 */

/**
 * Initialization Code and shared classes of library com.laravelui5.core
 */
sap.ui.define([
	"sap/base/util/ObjectPath",
	"sap/ui/core/library"
], function (ObjectPath) {
	"use strict";

	// delegate further initialization of this library to the Core
	// Hint: sap.ui.getCore() must still be used to support preload with sync bootstrap!
	sap.ui.getCore().initLibrary({
		name: "com.laravelui5.core",
		version: "3.3.1",
		dependencies: [
			// keep in sync with the ui5.yaml and .library files
			"sap.ui.core"
		],
		types: [],
		interfaces: [],
		controls: [],
		elements: [
			"com.laravelui5.core.BaseComponent",
			"com.laravelui5.core.BaseController",
			"com.laravelui5.core.Connection",
			"com.laravelui5.core.Context",
			"com.laravelui5.core.LaravelUi5"
		],
		noLibraryCSS: true // if no CSS is provided, you can disable the library.css load here
	});


	/**
	 * Some description about <code>com.laravelui5.core</code>
	 *
	 * @namespace
	 * @alias com.laravelui5.core
	 * @author Michael Gerzabek
	 * @version 3.3.1
	 * @public
	 */
	return ObjectPath.get("com.laravelui5.core");
});
