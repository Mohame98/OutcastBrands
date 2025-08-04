function handleAllPopovers() {
  const popovers = document.querySelectorAll(".popover");

  popovers.forEach(popover => {
    const popoverId = popover.id;
    const popoverBtn = document.querySelector(`[aria-controls="${popoverId}"]`);
    if (!popoverBtn) return;

    popover.addEventListener("toggle", event => {
      const isOpen = event.target.matches(":popover-open");
      popoverBtn.setAttribute("aria-expanded", isOpen ? "true" : "false");

      popoverId === 'comment-section' 
        ? document.body.classList.toggle('no-scroll', isOpen) 
        : null;
    });
  });
}

export {
  handleAllPopovers,
};
