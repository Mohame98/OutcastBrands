function handleModals() {
  const OPEN_ATTR = 'open';
  const ANIM_MS = 50; 

  const openStack = [];
  const openerMap = new WeakMap();

  const isDialog = (el) => el && el.tagName === 'DIALOG';
  const topDialog = () => openStack[openStack.length - 1] || null;

  const setExpanded = (trigger, val) => {
    if (!trigger) return;
    trigger.setAttribute('aria-expanded', String(val));
  };

  const resolveTargetDialog = (trigger) => {
    const sel = trigger.getAttribute('data-dialog-open');
    if (sel) return document.querySelector(sel);

    const wrapper = trigger.closest('.modal-wrapper');
    if (wrapper) return wrapper.querySelector('dialog');

    const id = trigger.getAttribute('aria-controls');
    if (id) return document.getElementById(id);

    return null;
  };

  const closeUnrelatedOpenDialogs = (target) => {
    const current = [...openStack];
    for (const dlg of current) {
      if (!dlg.contains(target) && !target.contains(dlg)) {
        closeDialog(dlg, { restoreFocus: false });
      }
    }
  };

  const syncTriggerAria = (dialog) => {
    const opener = openerMap.get(dialog);
    if (opener) {
      setExpanded(opener, dialog.hasAttribute(OPEN_ATTR));
      return;
    }
    const wrapper = dialog.closest('.modal-wrapper');
    const fallbackTrigger = wrapper?.querySelector('.modal-btn');
    if (fallbackTrigger) {
      setExpanded(fallbackTrigger, dialog.hasAttribute(OPEN_ATTR));
    }
  };

  const raf = () => new Promise(requestAnimationFrame);
  const nextFrame = () => raf().then(raf);

  const playOpenAnimation = async (dialog) => {
    dialog.classList.add('is-opening');

    await nextFrame();
    dialog.classList.add('is-open');
  };

  const playCloseAnimation = (dialog) =>
    new Promise((resolve) => {
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
      setTimeout(finish, ANIM_MS);
    });

  const openDialog = async (dialog, trigger = null) => {
    if (!isDialog(dialog)) return;

    if (dialog.open) {
      const idx = openStack.indexOf(dialog);
      if (idx !== -1 && idx !== openStack.length - 1) {
        openStack.splice(idx, 1);
        openStack.push(dialog);
      }
      dialog.focus();
      return;
    }

    closeUnrelatedOpenDialogs(dialog);

    if (trigger instanceof HTMLElement) openerMap.set(dialog, trigger);
    dialog.setAttribute('aria-modal', 'true');
    dialog.showModal();

    openStack.push(dialog);
    syncTriggerAria(dialog);

    await playOpenAnimation(dialog);
    dialog.focus();
    recomputeInert();
  };

  const closeDialog = async (dialog, { restoreFocus = true } = {}) => {
    if (!isDialog(dialog) || !dialog.open) return;

    await playCloseAnimation(dialog);
    dialog.close();

    const idx = openStack.indexOf(dialog);
    if (idx !== -1) openStack.splice(idx, 1);

    const opener = openerMap.get(dialog);
    if (restoreFocus && opener && document.contains(opener)) {
      setExpanded(opener, false);
      opener.focus({ preventScroll: true });
    }
    openerMap.delete(dialog);

    syncTriggerAria(dialog);
    recomputeInert();
  };

  document.addEventListener('click', (e) => {
    const openBtn = e.target.closest('[data-dialog-open], .modal-btn, a[href^="#"]');
    if (openBtn) {
      let target = null;

      if (openBtn.hasAttribute('data-dialog-open') || openBtn.classList.contains('modal-btn')) {
        target = resolveTargetDialog(openBtn);
      } else if (openBtn.tagName === 'A') {
        const href = openBtn.getAttribute('href');
        if (href && href.startsWith('#')) target = document.querySelector(href);
      }

      if (isDialog(target)) {
        e.preventDefault();
        openDialog(target, openBtn);
        return;
      }
    }

    const closeBtn = e.target.closest('[data-dialog-close], .close-modal');
    if (closeBtn) {
      const dlg = closeBtn.closest('dialog');
      if (dlg) {
        e.preventDefault();
        closeDialog(dlg);
      }
      return;
    }

    const dlg = e.target.closest('dialog');
    if (dlg && e.target === dlg && dlg === topDialog()) {
      e.preventDefault();
      closeDialog(dlg);
    }
  });

  const attachPerDialogHandlers = (dialog) => {
    if (dialog.__hasModalHandlers) return;
    dialog.__hasModalHandlers = true;

    dialog.addEventListener('cancel', (ev) => {
      ev.preventDefault(); 
      closeDialog(dialog);
    });

    const attrObserver = new MutationObserver((mutations) => {
      for (const m of mutations) {
        if (m.type === 'attributes' && m.attributeName === OPEN_ATTR) {
          syncTriggerAria(dialog);
        }
      }
    });
    attrObserver.observe(dialog, { attributes: true, attributeFilter: [OPEN_ATTR] });
  };

  document.querySelectorAll('dialog').forEach(attachPerDialogHandlers);

  const treeObserver = new MutationObserver((mutations) => {
    for (const m of mutations) {
      m.addedNodes.forEach((node) => {
        if (node.nodeType !== 1) return;
        if (node.tagName === 'DIALOG') attachPerDialogHandlers(node);
        node.querySelectorAll?.('dialog').forEach(attachPerDialogHandlers);
      });
    }
  });
  treeObserver.observe(document.body, { childList: true, subtree: true });

  function recomputeInert() {
    const bodyKids = Array.from(document.body.children);
    const openSet = new Set(openStack);

    for (const el of bodyKids) {
      const hostsOpenDialog = [...openSet].some(d => el === d || el.contains(d));
      if (hostsOpenDialog) el.removeAttribute('inert');
      else el.setAttribute('inert', '');
    }

    for (const dlg of openStack) {
      dlg.removeAttribute('inert');
      dlg.querySelectorAll('[inert]').forEach(n => n.removeAttribute('inert'));
    }

    if (!openStack.length) {
      for (const el of bodyKids) el.removeAttribute('inert');
    }
    document.body.classList.toggle('no-scroll', openStack.length > 0);
  }
}

export {
  handleModals,
};