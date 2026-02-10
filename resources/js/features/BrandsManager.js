/**
 * BrandsManager - Handles brand-related operations
 * Manages brand fetching, rendering, and pagination
 * FIXED: Isolated save/unsave state from global form state
 */

import { CONFIG } from "../config.js";
import { state } from "../core/StateManager.js";
import { dom } from "../core/DOMManager.js";
import APIService from "../services/APIService.js";
import UIService from "../services/UIService.js";

class BrandsManager {
    constructor() {
        this.loadMoreListenerAttached = false;
        this.unsaveListenerAttached = false;
    }

    /**
     * Initialize brands functionality
     */
    init() {
        this.fetchInitialBrands();
        this.attachLoadMoreListener();
    }

    /**
     * Fetch initial brands
     */
    async fetchInitialBrands() {
        const brandsContainer = dom.get("brandsContainer");
        if (!brandsContainer) return;

        const queryString = state.getQueryString();
        const signal = state.getAbortSignal();

        UIService.showLoading(brandsContainer);
        await this.fetchAndRenderBrands(queryString, signal);
        UIService.clearLoading(brandsContainer);
    }

    /**
     * Fetch and render brands
     * @param {string} queryString - Query parameters
     * @param {AbortSignal} signal - Abort signal
     * @param {boolean} append - Whether to append to existing content
     */
    async fetchAndRenderBrands(queryString, signal = null, append = false) {
        const result = await APIService.fetchBrands(queryString, signal);

        if (result.aborted) return;

        if (!result.success) {
            UIService.showError(result.error || CONFIG.MESSAGES.FETCH_ERROR);
            return;
        }

        const { html_cards, has_more_brands, total } = result.data;
        UIService.renderBrands(html_cards.join(""), append);
        UIService.updateLoadMoreButton(has_more_brands);
        if (typeof total === "number") UIService.updateBrandsCount(total);
    }

    /**
     * Attach load more button listener
     */
    attachLoadMoreListener() {
        if (this.loadMoreListenerAttached) return;

        const loadMoreBtn = dom.get("loadMoreBtn");
        if (!loadMoreBtn) return;

        loadMoreBtn.addEventListener("click", () => this.loadMore());
        this.loadMoreListenerAttached = true;
    }

    /**
     * Load more brands
     */
    async loadMore() {
        const loadMoreBtn = dom.get("loadMoreBtn");
        if (!loadMoreBtn) return;

        UIService.setLoadMoreButtonLoading(true, "loadMoreBtn");

        try {
            // Increment page
            const page = state.incrementPage("brands");
            state.setFilter("page", page);

            // Update URL and active filters display
            state.syncToURL();
            const filterManager = window.app?.getModule?.("filterManager");
            if (filterManager?.updateActiveFiltersDisplay) {
                filterManager.updateActiveFiltersDisplay();
            }

            // Fetch more brands
            const queryString = state.getQueryString();
            await this.fetchAndRenderBrands(queryString, null, true);
        } finally {
            UIService.setLoadMoreButtonLoading(false, "loadMoreBtn");
        }
    }
}

export default BrandsManager;