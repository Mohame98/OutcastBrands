/**
 * CommentsManager - Handles comment-related operations
 * Manages comment fetching, rendering, and pagination
 */

import { CONFIG } from "../config.js";
import { state } from "../core/StateManager.js";
import { dom } from "../core/DOMManager.js";
import APIService from "../services/APIService.js";
import UIService from "../services/UIService.js";

class CommentsManager {
  constructor() {
      this.loadMoreListenerAttached = false;
      this.brandId = null;
  }

  /**
   * Initialize comments functionality
   */
  init() {
    const commentsContainer = dom.get("commentsContainer");
    if (!commentsContainer) return;

    this.brandId = commentsContainer.dataset.brandId;
    if (!this.brandId) return;

    this.fetchInitialComments();
    this.attachLoadMoreListener();
  }

  /**
   * Fetch initial comments
   */
  async fetchInitialComments() {
    const commentsContainer = dom.get("commentsContainer");
    if (!commentsContainer) return;

    const queryString = state.getQueryString();
    const page = state.getPage("comments");
    const paginatedQuery = `${queryString}&page=${page}&limit=${CONFIG.COMMENTS_PER_PAGE}`;
    const signal = state.getAbortSignal();

    UIService.showLoading(commentsContainer);
    await this.fetchAndRenderComments(paginatedQuery, signal);
    UIService.clearLoading(commentsContainer);
  }

  /**
   * Fetch and render comments
   * @param {string} queryString - Query parameters
   * @param {AbortSignal} signal - Abort signal
   * @param {boolean} append - Whether to append to existing content
   */
  async fetchAndRenderComments(queryString, signal = null, append = false) {
    if (!this.brandId) return;

    const result = await APIService.fetchComments(
      this.brandId,
      queryString,
      signal,
    );

    if (result.aborted) return;

    if (!result.success) {
      UIService.showError(result.error || CONFIG.MESSAGES.FETCH_ERROR);
      return;
    }

    const { html_comments, has_more_comments } = result.data;
    UIService.renderComments(html_comments.join(""), append);
    UIService.updateLoadMoreButton(
      has_more_comments,
      "loadMoreCommentsBtn",
    );
  }

  /**
   * Attach load more button listener
   */
  attachLoadMoreListener() {
    if (this.loadMoreListenerAttached) return;

    const loadMoreBtn = dom.get("loadMoreCommentsBtn");
    if (!loadMoreBtn) return;

    loadMoreBtn.addEventListener("click", () => this.loadMore());
    this.loadMoreListenerAttached = true;
  }

  /**
   * Load more comments
   */
  async loadMore() {
    const loadMoreBtn = dom.get("loadMoreCommentsBtn");
    if (!loadMoreBtn) return;

    UIService.setLoadMoreButtonLoading(true, "loadMoreCommentsBtn");

    try {
      // Increment page
      const page = state.incrementPage("comments");
      state.setFilter("page", page);

      // Update URL
      state.syncToURL();

      // Fetch more comments
      const queryString = state.getQueryString();
      const paginatedQuery = `${queryString}&page=${page}&limit=${CONFIG.COMMENTS_PER_PAGE}`;

      await this.fetchAndRenderComments(paginatedQuery, null, true);
    } finally {
      UIService.setLoadMoreButtonLoading(false, "loadMoreCommentsBtn");
    }
  }

  /**
   * Add a new comment to the UI
   * @param {string} commentHTML - Comment HTML
   * @param {number} newCount - New comment count
   */
  addComment(commentHTML, newCount) {
    const commentsContainer = dom.get("commentsContainer");
    if (!commentsContainer) return;

    commentsContainer.insertAdjacentHTML("beforeend", commentHTML);
    UIService.updateCommentCount(newCount);
  }

  /**
   * Remove a comment from the UI
   * @param {number} commentId - Comment ID
   * @param {number} newCount - New comment count
   */
  removeComment(commentId, newCount) {
    const commentElement = document.querySelector(`#comment-${commentId}`);
    if (commentElement) {
      commentElement.remove();
      UIService.updateCommentCount(newCount);
    }
  }

  /**
   * Update a comment in the UI
   * @param {number} commentId - Comment ID
   * @param {string} commentHTML - Updated comment HTML
   */
  updateComment(commentId, commentHTML) {
    const commentElement = document.querySelector(`#comment-${commentId}`);
    if (commentElement) {
      commentElement.outerHTML = commentHTML;
    }
  }
}

export default CommentsManager;
