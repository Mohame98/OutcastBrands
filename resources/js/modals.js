function handleModals() {
  document.addEventListener('click', (event) => {
    const modalBtn = event.target.closest('.modal-btn');
    
    if (modalBtn) {
      const wrapper = modalBtn.closest('.modal-wrapper');
      if (!wrapper) return;

      const dialog = wrapper.querySelector('dialog');
      if (!dialog) return;
      dialog.showModal();

      const isOpen = dialog.open;
      modalBtn.setAttribute('aria-expanded', isOpen.toString());
      document.body.classList.add('no-scroll');
    }

    const dialog = event.target.closest('dialog');
    if (dialog && event.target === dialog) {
      dialog.close();
      const modalBtn = dialog.closest('.modal-wrapper').querySelector('.modal-btn');
      if (modalBtn) modalBtn.setAttribute('aria-expanded', 'false');
      document.body.classList.remove('no-scroll');
    }

    const closeBtn = event.target.closest('.close-modal');
    if (closeBtn) {
      const dialog = closeBtn.closest('dialog');
      if (!dialog) return;
      dialog.close();
      const modalBtn = dialog.closest('.modal-wrapper').querySelector('.modal-btn');
      if (modalBtn) modalBtn.setAttribute('aria-expanded', 'false');
      document.body.classList.remove('no-scroll');
    }
  });

  document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
      const dialog = document.querySelector('dialog[open]');
      if (!dialog) return;
      dialog.close();
      const modalBtn = dialog.closest('.modal-wrapper').querySelector('.modal-btn');
      if (modalBtn) modalBtn.setAttribute('aria-expanded', 'false');
      document.body.classList.remove('no-scroll');
    }
  });
}

export {
  handleModals,
};