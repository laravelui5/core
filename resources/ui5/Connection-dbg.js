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
	 * @version 2.3.5
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
		 * Returns the unwrapped response `data` payload.
		 */
		async fetchGet(path) {
			const response = await fetch(`${this.baseUrl}${path}`, {
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
			return result.data;
		}

		/**
		 * Fetches XML data from a backend endpoint under the base URL.
		 * Parses and returns the XML document.
		 */
		async fetchXml(path) {
			const response = await fetch(`${this.baseUrl}${path}`, {
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

			if (!response.ok) {
				const errorText = await response.text();
				throw new Error(`Request failed: ${response.status} ${response.statusText}\n${errorText}`);
			}

			return await response.text();
		}

		/**
		 * Fetches HTML data from a backend endpoint under the base URL.
		 * Parses and returns the HTML document.
		 */
		async fetchHtml(path) {
			const response = await fetch(`${this.baseUrl}${path}`, {
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

			if (!response.ok) {
				throw new Error(`Failed to fetch HTML from '${path}' (status ${response.status})`);
			}

			return await response.text();
		}

		/**
		 * Submits a POST request to a backend URL with JSON body.
		 * Returns the parsed JSON result.
		 */
		async fetchPost(path, data) {
			const response = await fetch(`${this.baseUrl}${path}`, {
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
			const response = await fetch(`${this.baseUrl}${path}`, {
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
			const response = await fetch(`${this.baseUrl}${path}`, {
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
			const response = await fetch(`${this.baseUrl}${path}`, {
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
			if (!this.serviceUrl) {
				throw new Error("[Connection] serviceUrl is not initialized.");
			}
			const response = await fetch(`${this.serviceUrl}${path}`, {
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
		 * @returns {Promise<object>}
		 */
		async handleJsonResponse(response) {
			if (!response.ok) {
				const errorData = await this.tryParseJson(response).catch(() => ({}));
				const error = new Error(response.statusText);
				error.cause = errorData;
				throw error;
			}
			return this.tryParseJson(response);
		}

		/**
		 * Attempts to parse a JSON body from a fetch response.
		 * Returns `{}` if the body is empty or invalid.
		 *
		 * @param {Response} response
		 * @returns {Promise<object>}
		 */
		async tryParseJson(response) {
			try {
				const text = await response.text();
				return text ? JSON.parse(text) : {};
			} catch (e) {
				return {};
			}
		}
	}
});
