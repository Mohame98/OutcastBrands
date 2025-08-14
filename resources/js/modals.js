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