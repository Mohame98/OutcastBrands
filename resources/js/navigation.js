function handleNavOnScroll() {
  const nav = document.querySelector('.main-nav');
  const scrollThreshold = 50; 

  window.addEventListener('scroll', () => {
    if (window.scrollY > scrollThreshold) {
      nav.classList.add('scrolled');
    } else {
      nav.classList.remove('scrolled');
    }
  });
}

export {
  handleNavOnScroll,
};
