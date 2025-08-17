function handleModals() {
  document.addEventListener('click', (event) => {
    const modalBtn = event.target.closest('.modal-btn');
    if (modalBtn) {
      const wrapper = modalBtn.closest('.modal-wrapper');
      const dialog = wrapper?.querySelector('dialog');
      if (!dialog) return;
      dialog.showModal();
      document.body.classList.add('no-scroll');
    }

    const dialog = event.target.closest('dialog');
    if (dialog && event.target === dialog) {
      dialog.close();
      document.body.classList.remove('no-scroll');
    }

    const closeBtn = event.target.closest('.close-modal');
    if (closeBtn) {
      const dialog = closeBtn.closest('dialog');
      if (!dialog) return;
      dialog.close();
      document.body.classList.remove('no-scroll');
    }
  });

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
      const dialog = document.querySelector('dialog[open]');
      if (dialog) {
        dialog.close();
        document.body.classList.remove('no-scroll');
      }
    }
  });

  const observer = new MutationObserver((mutations) => {
    mutations.forEach(mutation => {
      if (
        mutation.type === 'attributes' &&
        mutation.attributeName === 'open'
      ) {
        const dialog = mutation.target;
        const modalWrapper = dialog.closest('.modal-wrapper');
        const modalBtn = modalWrapper?.querySelector('.modal-btn');
        if (modalBtn) {
          const isOpen = dialog.hasAttribute('open');
          modalBtn.setAttribute('aria-expanded', isOpen.toString());
        }
      }
    });
  });

  const dialogs = document.querySelectorAll('dialog');
  dialogs.forEach(dialog => {
    observer.observe(dialog, {
      attributes: true,
      attributeFilter: ['open'],
    });
  });
}

function toggleLoginModals(){
  function toggleModals(modalToShow, modalToClose) {
    if (modalToClose.open) {
      modalToClose.close(); 
    }
    
    if (!modalToShow.open) {
      modalToShow.showModal();
    }
  }
  const signInBtns = document.querySelectorAll('.second-sign-in-btn');
  const signUpBtns = document.querySelectorAll('.second-sign-up-btn');
  const forgotPassBtns = document.querySelectorAll('.forgot-second-sign-in-btn')

  const signUpModal = document.querySelector('.signup-modal');
  const signInModal = document.querySelector('.signin-modal');
  const forgotPassModal = document.querySelector('.forgot-password-modal');

  signInBtns.forEach(button => {
    button.addEventListener('click', function () {
      toggleModals(signInModal, signUpModal);
    });
  });

  signUpBtns.forEach(button => {
    button.addEventListener('click', function () {
      toggleModals(signUpModal, signInModal);
    });
  });

  forgotPassBtns.forEach(button => {
    button.addEventListener('click', function () {
      forgotPassModal.close();    
    });
  });
}

export {
  handleModals,
  toggleLoginModals,
};