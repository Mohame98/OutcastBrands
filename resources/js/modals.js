export function handleModals() {
  const allModalBtns = document.querySelectorAll('.modal-btn');
  allModalBtns.forEach((btn) => {
    btn.addEventListener('click', function (event) {
      const dialog = event.target.closest('.modal-wrapper')?.querySelector('dialog');
      const closeButton = dialog.querySelectorAll('.close-modal');
      if (dialog) {
        dialog.showModal();
        closeButton.forEach((closeBtn) => {
          closeBtn.addEventListener('click', function(){
            dialog.close();
          });
        });
        
        dialog.addEventListener('click', function(event){
          if (event.target === dialog) dialog.close();
        });
        dialog.addEventListener('toggle', function(event){
          const isOpen = event.target.matches('dialog:open');
          isOpen
          ? (
              document.body.classList.add('no-scroll'),
              btn.setAttribute('aria-expanded', 'true')
            )
          : (
              document.body.classList.remove('no-scroll'),
              btn.setAttribute('aria-expanded', 'false')
            )
        });
      }
    });
  });
}