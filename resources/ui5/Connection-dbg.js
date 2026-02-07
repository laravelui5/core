sap.ui.define([
	"sap/ui/model/odata/v4/ODataModel"
], function (
	ODataModel
) {
	"use strict";

	/**
	 * CSRF handling for UI5 applications.
	 *
	 * Connects the current UI5 component to a stateful Laravel backend.
	 *
	 * Includes helpers for CSRF-safe fetch operations.
	 *
	 * @author Michael Gerzabek
	 * @version 3.3.1
	 *
	 * @public
	 * @name com.laravelui5.core.Connection
	 */
	return class Connection {

		constructor(uri) {
			if (uri && typeof uri === "string" && uri.trim() !== "") {
				this.serviceUrl = uri.replace(/\/$/, "");
				const url = new URL(uri, window.location.origin);
				this.baseUrl = url.origin;
				console.info("[Connection] serviceUrl set to " + this.serviceUrl.toString());
			} else {
				this.baseUrl = window.location.origin;
				console.info("[Connection] mainService URI not found in manifest.");
			}
			console.info("[Connection] baseUrl set to " + this.baseUrl.toString());
		}

		/**
		 * Returns the service URI of the main odata service (base URL + path to service endpoint).
		 */
		getMainServiceUri() {
			return this.serviceUrl;
		}

		/**
		 * Returns the base URL (protocol + host) of the application.
		 */
		getBaseUrl() {
			return this.baseUrl;
		}

		/**
		 * Retrieves the XSRF token from the browser's cookies.
		 * Searches for a cookie named "XSRF-TOKEN" and extracts its value.
		 *
		 * @return {string|null} The decoded XSRF token if found, or null if the cookie is not present.
		 */
		getXsrfToken() {
			const match = document.cookie.match(/XSRF-TOKEN=([^;]+)/);
			return match ? decodeURIComponent(match[1]) : null;
		}

		/**
		 * Fetches JSON data from a backend endpoint under the base URL.
		 * Returns the unwrapped response payload.
		 */
		async fetchGet(path) {
			const serviceUrl = this.getBaseUrl();
			const normalized = String(path).trim().replace(/^\/+/, "");
			const response = await fetch(`${serviceUrl}/${normalized}`, {
				method: "GET",
				mode: "cors",
				cache: "no-cache",
				credentials: "same-origin",
				redirect: "follow",
				referrerPolicy: "same-origin",
				headers: {
					"Accept": "application/json",
					"X-XSRF-TOKEN": this.getXsrfToken()
				}
			});

			return await this.handleJsonResponse(response);
		}

		/**
		 * Fetches XML data from a backend endpoint under the base URL.
		 * Returns the XML document as a string.
		 */
		async fetchXml(path) {
			const serviceUrl = this.getBaseUrl();
			const normalized = String(path).trim().replace(/^\/+/, "");
			const response = await fetch(`${serviceUrl}/${normalized}`, {
				method: "GET",
				mode: "cors",
				cache: "no-cache",
				credentials: "same-origin",
				redirect: "follow",
				referrerPolicy: "same-origin",
				headers: {
					"Accept": "application/xml",
					"X-XSRF-TOKEN": this.getXsrfToken()
				}
			});

			return await this.handleTextResponse(response);
		}

		/**
		 * Fetches HTML data from a backend endpoint under the base URL.
		 * Returns the raw HTML markup as a string.
		 */
		async fetchHtml(path) {
			const serviceUrl = this.getBaseUrl();
			const normalized = String(path).trim().replace(/^\/+/, "");
			const response = await fetch(`${serviceUrl}/${normalized}`, {
				method: "GET",
				mode: "cors",
				cache: "no-cache",
				credentials: "same-origin",
				redirect: "follow",
				referrerPolicy: "same-origin",
				headers: {
					"Accept": "text/html",
					"X-XSRF-TOKEN": this.getXsrfToken()
				}
			});

			return await this.handleTextResponse(response);
		}

		/**
		 * Submits a POST request to a backend URL with JSON body.
		 * Returns the parsed JSON result.
		 */
		async fetchPost(path, data) {
			const serviceUrl = this.getBaseUrl();
			const normalized = String(path).trim().replace(/^\/+/, "");
			const response = await fetch(`${serviceUrl}/${normalized}`, {
				method: "POST",
				mode: "cors",
				cache: "no-cache",
				credentials: "same-origin",
				redirect: "follow",
				referrerPolicy: "same-origin",
				headers: {
					"Accept": "application/json",
					"Content-Type": "application/json",
					"X-XSRF-TOKEN": this.getXsrfToken()
				},
				body: JSON.stringify(data)
			});

			return await this.handleJsonResponse(response);
		}

		/**
		 * Submits a PATCH request to a backend URL with JSON body.
		 * Returns the parsed JSON result.
		 */
		async fetchPatch(path, data) {
			const serviceUrl = this.getBaseUrl();
			const normalized = String(path).trim().replace(/^\/+/, "");
			const response = await fetch(`${serviceUrl}/${normalized}`, {
				method: "PATCH",
				mode: "cors",
				cache: "no-cache",
				credentials: "same-origin",
				redirect: "follow",
				referrerPolicy: "same-origin",
				headers: {
					"Accept": "application/json",
					"Content-Type": "application/json",
					"X-XSRF-TOKEN": this.getXsrfToken()
				},
				body: JSON.stringify(data)
			});

			return await this.handleJsonResponse(response);
		}

		/**
		 * Submits a PUT request to a backend URL with JSON body.
		 * Returns the parsed JSON result.
		 */
		async fetchPut(path, data) {
			const serviceUrl = this.getBaseUrl();
			const normalized = String(path).trim().replace(/^\/+/, "");
			const response = await fetch(`${serviceUrl}/${normalized}`, {
				method: "PUT",
				mode: "cors",
				cache: "no-cache",
				credentials: "same-origin",
				redirect: "follow",
				referrerPolicy: "same-origin",
				headers: {
					"Accept": "application/json",
					"Content-Type": "application/json",
					"X-XSRF-TOKEN": this.getXsrfToken()
				},
				body: JSON.stringify(data)
			});

			return await this.handleJsonResponse(response);
		}


		/**
		 * Submits a DELETE request to a backend URL with JSON body.
		 * Returns the parsed JSON result.
		 */
		async fetchDelete(path, data) {
			const serviceUrl = this.getBaseUrl();
			const normalized = String(path).trim().replace(/^\/+/, "");
			const response = await fetch(`${serviceUrl}/${normalized}`, {
				method: "DELETE",
				mode: "cors",
				cache: "no-cache",
				credentials: "same-origin",
				redirect: "follow",
				referrerPolicy: "same-origin",
				headers: {
					"Accept": "application/json",
					"Content-Type": "application/json",
					"X-XSRF-TOKEN": this.getXsrfToken()
				},
				body: data ? JSON.stringify(data) : undefined
			});

			return await this.handleJsonResponse(response);
		}

		/**
		 * Fetches OData EntitySet from a service-relative endpoint.
		 * Returns the unwrapped response value array.
		 */
		async fetchEntitySet(path) {
			const serviceUrl = this.getMainServiceUri();
			if (!serviceUrl) {
				throw new Error("[Connection] serviceUrl is not initialized.");
			}
			const normalized = String(path).trim().replace(/^\/+/, "");
			const response = await fetch(`${serviceUrl}/${normalized}`, {
				method: "GET",
				mode: "cors",
				cache: "no-cache",
				credentials: "same-origin",
				redirect: "follow",
				referrerPolicy: "same-origin",
				headers: {
					"Accept": "application/json",
					"X-XSRF-TOKEN": this.getXsrfToken()
				}
			});

			const result = await this.handleJsonResponse(response);

			return result.value;
		}

		/**
		 * Handles a fetch response with JSON body and standardized error propagation.
		 *
		 * - If the response is not OK (status 4xx/5xx), it tries to extract a JSON body
		 *   and throws an error with that data in `cause`.
		 * - If no JSON body exists, it falls back to a status text.
		 * - On success, it parses and returns the JSON result.
		 *
		 * @param {Response} response
		 * @returns {Promise<object|null>}
		 *
		 * @private
		 */
		async handleJsonResponse(response) {

			let payload = null;
			if (response.status !== 204) {
				try {
					payload = await response.json();
				} catch (e) {
					payload = null;
				}
			}

			if (!response.ok) {
				const error = new Error(response.statusText);
				error.status = response.status;
				error.cause = payload;
				throw error;
			}

			return payload;
		}

		/**
		 * Handles a fetch response with a textual body and standardized error propagation.
		 *
		 * - Reads the response body as text (or returns null for 204 No Content).
		 * - If the response is not OK (4xx/5xx), throws an Error containing
		 *   the HTTP status and the raw response payload as `cause`.
		 * - On success, returns the raw response text.
		 *
		 * @param response
		 * @return {Promise<string|null>}
		 *
		 * @private
		 */
		async handleTextResponse(response) {

			let payload = null;
			if (response.status !== 204) {
				try {
					payload = await response.text();
				} catch (_) {
					payload = null;
				}
			}

			if (!response.ok) {
				const error = new Error(response.statusText);
				error.status = response.status;
				error.cause = payload;
				throw error;
			}

			return payload;
		}
	}
});
