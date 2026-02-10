/**
 * ModalManager - Manages native <dialog> elements
 * Sophisticated implementation with animations, stacking, and proper ARIA
 * Based on native HTML dialog API with enhanced UX
 */

class ModalManager {
  constructor() {
    this.openStack = [];
    this.openerMap = new WeakMap();
    this.OPEN_ATTR = 'open';
    this.ANIM_MS = 200;
  }

  /**
   * Initialize modal handling
   */
  init() {
    this.attachGlobalListeners();
    this.setupExistingDialogs();
    this.watchForNewDialogs();
    return true;
  }
  
  /**
   * Check if element is a dialog
   */
  isDialog(el) {
    return el && el.tagName === 'DIALOG';
  }

  /**
   * Get the top-most dialog
   */
  topDialog() {
    return this.openStack[this.openStack.length - 1] || null;
  }

  /**
   * Set aria-expanded on trigger
   */
  setExpanded(trigger, value) {
    if (!trigger) return;
    trigger.setAttribute('aria-expanded', String(value));
  }

  /**
   * Resolve target dialog from trigger
   */
  resolveTargetDialog(trigger) {
    // Check data-dialog-open attribute
    const selector = trigger.getAttribute('data-dialog-open');
    if (selector) return document.querySelector(selector);

    // Check modal-wrapper pattern
    const wrapper = trigger.closest('.modal-wrapper');
    if (wrapper) return wrapper.querySelector('dialog');

    // Check aria-controls
    const id = trigger.getAttribute('aria-controls');
    if (id) return document.getElementById(id);

    return null;
  }

  /**
   * Close unrelated open dialogs
   */
  closeUnrelatedOpenDialogs(target) {
    const current = [...this.openStack];
    for (const dlg of current) {
      if (!dlg.contains(target) && !target.contains(dlg)) {
        this.closeDialog(dlg, { restoreFocus: false });
      }
    }
  }

  /**
   * Sync trigger ARIA attributes
   */
  syncTriggerAria(dialog) {
    const opener = this.openerMap.get(dialog);
    if (opener) {
      this.setExpanded(opener, dialog.hasAttribute(this.OPEN_ATTR));
      return;
    }

    const wrapper = dialog.closest('.modal-wrapper');
    const fallbackTrigger = wrapper?.querySelector('.modal-btn');
    if (fallbackTrigger) {
      this.setExpanded(fallbackTrigger, dialog.hasAttribute(this.OPEN_ATTR));
    }
  }

  /**
   * Request animation frame utilities
   */
  raf() {
    return new Promise(requestAnimationFrame);
  }

  nextFrame() {
    return this.raf().then(() => this.raf());
  }

  /**
   * Play open animation
   */
  async playOpenAnimation(dialog) {
    dialog.classList.add('is-opening');
    await this.nextFrame();
    dialog.classList.add('is-open');
  }

  /**
   * Play close animation
   */
  playCloseAnimation(dialog) {
    return new Promise((resolve) => {
      dialog.classList.remove('is-open');
      dialog.classList.add('is-closing');

      let done = false;
      const finish = () => {
        if (done) return;
        done = true;
        dialog.removeEventListener('transitionend', onEnd);
        dialog.classList.remove('is-opening', 'is-closing');
        resolve();
      };

      const onEnd = (e) => {
        if (e.target === dialog) finish();
      };

      dialog.addEventListener('transitionend', onEnd);
      setTimeout(finish, this.ANIM_MS);
    });
  }

  /**
   * Open a dialog
   * @param {HTMLElement} dialog - Dialog element
   * @param {HTMLElement} trigger - Trigger element (optional)
   */
  async openDialog(dialog, trigger = null) {
    if (!this.isDialog(dialog)) return;

    dialog.querySelectorAll('input, textarea').forEach(input => {
      input.value = input.value.trim();
    });

    // If already open, just refocus
    if (dialog.open) {
      const idx = this.openStack.indexOf(dialog);
      if (idx !== -1 && idx !== this.openStack.length - 1) {
        this.openStack.splice(idx, 1);
        this.openStack.push(dialog);
      }
      dialog.focus();
      return;
    }

    this.closeUnrelatedOpenDialogs(dialog);

    if (trigger instanceof HTMLElement) {
      this.openerMap.set(dialog, trigger);
    }

    dialog.setAttribute('aria-modal', 'true');
    dialog.showModal();

    this.openStack.push(dialog);
    this.syncTriggerAria(dialog);

    await this.playOpenAnimation(dialog);
    this.recomputeInert();
  }

  /**
   * Close a dialog
   * @param {HTMLElement} dialog - Dialog element
   * @param {Object} options - Options
   */
  async closeDialog(dialog, { restoreFocus = true } = {}) {
    if (!this.isDialog(dialog) || !dialog.open) return;

    await this.playCloseAnimation(dialog);
    dialog.close();

    const idx = this.openStack.indexOf(dialog);
    if (idx !== -1) this.openStack.splice(idx, 1);

    const opener = this.openerMap.get(dialog);
    if (restoreFocus && opener && document.contains(opener)) {
      this.setExpanded(opener, false);
      opener.focus({ preventScroll: true });
    }
    this.openerMap.delete(dialog);

    this.syncTriggerAria(dialog);
    this.recomputeInert();
  }

  /**
   * Attach global event listeners
   */
  attachGlobalListeners() {
    document.addEventListener('click', (e) => {
      // Open dialog
      const openBtn = e.target.closest('[data-dialog-open], .modal-btn, a[href^="#"]');
      if (openBtn) {
        let target = null;

        if (openBtn.hasAttribute('data-dialog-open') || openBtn.classList.contains('modal-btn')) {
          target = this.resolveTargetDialog(openBtn);
        } else if (openBtn.tagName === 'A') {
          const href = openBtn.getAttribute('href');
          if (href && href.startsWith('#')) {
            target = document.querySelector(href);
          }
        }

        if (this.isDialog(target)) {
          e.preventDefault();
          this.openDialog(target, openBtn);
          return;
        }
      }

      // Close dialog
      const closeBtn = e.target.closest('[data-dialog-close], .close-modal');
      if (closeBtn) {
        const dlg = closeBtn.closest('dialog');
        if (dlg) {
          e.preventDefault();
          this.closeDialog(dlg);
        }
        return;
      }

      // Click backdrop to close
      const dlg = e.target.closest('dialog');
      if (dlg && e.target === dlg && dlg === this.topDialog()) {
        e.preventDefault();
        this.closeDialog(dlg);
      }
    });
  }

  /**
   * Setup handlers for existing dialogs
   */
  setupExistingDialogs() {
    document.querySelectorAll('dialog').forEach(dialog => {
      this.attachPerDialogHandlers(dialog);
    });
  }

  /**
   * Attach handlers to individual dialog
   */
  attachPerDialogHandlers(dialog) {
    if (dialog.__hasModalHandlers) return;
    dialog.__hasModalHandlers = true;

    // Handle cancel event (ESC key)
    dialog.addEventListener('cancel', (ev) => {
      ev.preventDefault();
      this.closeDialog(dialog);
    });

    // Watch for attribute changes
    const attrObserver = new MutationObserver((mutations) => {
      for (const m of mutations) {
        if (m.type === 'attributes' && m.attributeName === this.OPEN_ATTR) {
          this.syncTriggerAria(dialog);
        }
      }
    });
    attrObserver.observe(dialog, {
      attributes: true,
      attributeFilter: [this.OPEN_ATTR],
    });
  }

  /**
   * Watch for new dialogs added to DOM
   */
  watchForNewDialogs() {
    const treeObserver = new MutationObserver((mutations) => {
      for (const m of mutations) {
        m.addedNodes.forEach((node) => {
          if (node.nodeType !== 1) return;
          if (node.tagName === 'DIALOG') {
            this.attachPerDialogHandlers(node);
          }
          node.querySelectorAll?.('dialog').forEach(dlg => {
            this.attachPerDialogHandlers(dlg);
          });
        });
      }
    });

    treeObserver.observe(document.body, {
      childList: true,
      subtree: true,
    });
  }

  /**
   * Recompute inert attributes
   * Makes content outside dialogs non-interactive
   */
  recomputeInert() {
    const bodyKids = Array.from(document.body.children);
    const openSet = new Set(this.openStack);

    for (const el of bodyKids) {
      const hostsOpenDialog = [...openSet].some(d => el === d || el.contains(d));
      if (hostsOpenDialog) {
        el.removeAttribute('inert');
      } else {
        el.setAttribute('inert', '');
      }
    }

    for (const dlg of this.openStack) {
      dlg.removeAttribute('inert');
      dlg.querySelectorAll('[inert]').forEach(n => n.removeAttribute('inert'));
    }

    if (!this.openStack.length) {
      for (const el of bodyKids) {
        el.removeAttribute('inert');
      }
    }

    document.body.classList.toggle('no-scroll', this.openStack.length > 0);
  }

  /**
   * Public API: Open dialog by ID
   */
  open(dialogId, trigger = null) {
    const dialog = document.querySelector(`#${dialogId}`);
    if (dialog) this.openDialog(dialog, trigger);
  }

  /**
   * Public API: Close specific or top dialog
   */
  close(dialogId = null) {
    if (dialogId) {
      const dialog = document.querySelector(`#${dialogId}`);
      if (dialog) this.closeDialog(dialog);
    } else {
      const top = this.topDialog();
      if (top) this.closeDialog(top);
    }
  }

  /**
   * Public API: Close all dialogs
   */
  closeAll() {
    const current = [...this.openStack];
    current.forEach(dlg => this.closeDialog(dlg, { restoreFocus: false }));
  }

  /**
   * Check if any dialog is open
   */
  isOpen() {
    return this.openStack.length > 0;
  }

  /**
   * Get active dialog
   */
  getActiveDialog() {
    return this.topDialog();
  }

  /**
   * Destroy and cleanup
   */
  destroy() {
    this.closeAll();
    this.openerMap = new WeakMap();
  }
}
  
export default ModalManager;