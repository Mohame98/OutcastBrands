/**
 * APIService - Handles all HTTP requests
 * Centralized location for API calls with error handling
 */

import { CONFIG } from "../config.js";

class APIService {
  /**
   * Make a fetch request with standard error handling
   * @param {string} url - Request URL
   * @param {Object} options - Fetch options
   * @returns {Promise<Object>} Response data
   */
  static async request(url, options = {}) {
    try {
      const response = await fetch(url, {
        credentials: "same-origin",
        headers: {
          'Content-Type': 'application/json',
          Accept: "application/json",
          ...options.headers,
        },
        ...options,
      });

      const contentType = response.headers.get("Content-Type") || "";
      let data;

      if (contentType.includes("application/json")) {
        data = await response.json();
      } else {
          const text = await response.text();
          if (text.trimStart().startsWith("<")) {
            return {
              success: false,
              status: response.status,
              error: {
                message:
                  response.status === 404
                    ? "Page not found. Check the request URL."
                    : response.status === 419
                      ? "Session expired. Please refresh and try again."
                      : "Server returned a page instead of JSON. You may need to sign in or the URL may be incorrect.",
              },
            };
          }
        try {
          data = JSON.parse(text);
        } catch {
          return {
            success: false,
            status: response.status,
            error: { message: text || "Invalid response" },
          };
        }
      }

      if (!response.ok) {
        return {
          success: false,
          status: response.status,
          error: data,
        };
      }

      return {
        success: true,
        status: response.status,
        data,
      };
    } catch (error) {
      if (error.name === "AbortError") {
        console.log("Request cancelled");
        return { success: false, aborted: true };
      }

      console.error("API Error:", error);
      return {
        success: false,
        error: error.message || CONFIG.MESSAGES.NETWORK_ERROR,
      };
    }
  }

  /**
   * Fetch brand cards
   * @param {string} queryString - Query parameters
   * @param {AbortSignal} signal - Abort signal for cancellation
   * @returns {Promise<Object>}
   */
  static async fetchBrands(queryString, signal = null) {
    const path = window.location.pathname;
    const url = this.getBrandsURL(path, queryString);

    if (!url) {
      return { success: false, error: "Invalid route" };
    }

    return this.request(url, { signal });
  }

  /**
   * Fetch comments
   * @param {number} brandId - Brand ID
   * @param {string} queryString - Query parameters
   * @param {AbortSignal} signal - Abort signal for cancellation
   * @returns {Promise<Object>}
   */
  static async fetchComments(brandId, queryString, signal = null) {
    const url = `/api/brands/${brandId}/comments?${queryString}`;
    return this.request(url, { signal });
  }

  /**
   * Submit a form
   * @param {string} url - Form action URL
   * @param {FormData} formData - Form data
   * @param {string} method - HTTP method
   * @param {string} csrfToken - CSRF token
   * @returns {Promise<Object>}
   */
  static async submitForm(url, formData, method = "POST", csrfToken = "") {
    return this.request(url, {
      method,
      body: formData,
      headers: {
        "X-Requested-With": "XMLHttpRequest",
        "X-CSRF-TOKEN": csrfToken,
      },
    });
  }

  /**
   * Get brands URL based on current route
   * @param {string} path - Current path
   * @param {string} queryString - Query parameters
   * @returns {string|null}
   */
  static getBrandsURL(path, queryString) {
    const paginatedQuery = `${queryString}&limit=${CONFIG.BRANDS_PER_PAGE}`;

    for (const route of Object.values(CONFIG.ROUTES)) {
      if (route.pattern.test(path)) {
        return route.getUrl(path, paginatedQuery);
      }
    }

    return null;
  }
}

export default APIService;
