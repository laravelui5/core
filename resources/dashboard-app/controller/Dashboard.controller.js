sap.ui.define([
    "sap/ui/core/mvc/Controller"
], function (Controller) {
    "use strict";

    return Controller.extend("io.pragmatiqu.dashboard.controller.Dashboard", {
        /**
         * Convenience method for accessing the component of the controller's view.
         * @returns {sap.ui.core.Component} The component of the controller's view
         */
        getOwnerComponent: function () {
            return Controller.prototype.getOwnerComponent.call(this);
        },

        /**
         * Convenience method for getting the i18n resource bundle of the component.
         * @returns {Promise<sap.base.i18n.ResourceBundle>} The i18n resource bundle of the component
         */
        getResourceBundle: function () {
            const oModel = this.getOwnerComponent().getModel("i18n");
            return oModel.getResourceBundle();
        },

        /**
         * Convenience method for getting the view model by name in every controller of the application.
         * @param {string} [sName] The model name
         * @returns {sap.ui.model.Model} The model instance
         */
        getModel: function (sName) {
            return this.getView().getModel(sName);
        },

        /**
         * Convenience method for setting the view model in every controller of the application.
         * @param {sap.ui.model.Model} oModel The model instance
         * @param {string} [sName] The model name
         * @returns {sap.ui.core.mvc.Controller} The current base controller instance
         */
        setModel: function (oModel, sName) {
            this.getView().setModel(oModel, sName);
            return this;
        },
    });
});
