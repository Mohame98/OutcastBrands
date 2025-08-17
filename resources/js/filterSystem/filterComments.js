import {
  updateActiveFilters,
} from './mainFilterSys';	

let commentsPage = 1;

async function fetchComments(brandId, queryString, page, filters) {
  const commentsPerPage = 5;
  const paginatedQuery = `${queryString}&page=${page}&limit=${commentsPerPage}`;
  const response = await fetch(`/api/brands/${brandId}/comments?${paginatedQuery}`);
  if (!response.ok) throw new Error("Failed to fetch comments");
  const data = await response.json();
  renderComments(data.html_comments.join(""));
  handleLoadMoreCommentsButton(data.has_more_comments, brandId, filters);
}

function renderComments(html) {
  const container = document.querySelector("#comments-container");
  if (!container) return;
  const parser = new DOMParser();
  const doc = parser.parseFromString(html, 'text/html');
  const comments = doc.body.children;
  Array.from(comments).forEach(el => container.appendChild(el));
}

let loadMoreCommentsListenerAttached = false;
function handleLoadMoreCommentsButton(hasMore, brandId, filters) {
  const btn = document.querySelector(".load-more-comments");
  if (!btn) return;
  if (hasMore) {
    btn.style.display = 'block';
    if (!loadMoreCommentsListenerAttached) {
      btn.addEventListener("click", () => {
        btn.disabled = true;
        commentsPage++;
        filters.set("page", commentsPage);
        const queryString = filters.toString();
        window.history.replaceState(null, "", `${window.location.pathname}?${queryString}`);
        fetchComments(brandId, queryString, commentsPage, filters)
          .finally(() => {
            btn.disabled = false;
        });
      });
      loadMoreCommentsListenerAttached = true;
    }
  } else {
    btn.style.display = 'none';
  }
}

function initComments(filters) {
  const commentsContainer = document.querySelector('#comments-container');
  if (!commentsContainer) return;
  const brandId = commentsContainer.dataset.brandId;
  
  updateActiveFilters();
  const initialQuery = filters.toString();
  fetchComments(brandId, initialQuery, commentsPage, filters);
}

export {
  initComments
};