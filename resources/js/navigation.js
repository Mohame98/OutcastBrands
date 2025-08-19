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

function mobileMenuToggle() {
  const menuBtn = document.querySelector('.mobile-menu-btn');
  if (!menuBtn) return;
  menuBtn.addEventListener("click", function () {
    const menuLoginLinks = document.querySelector(".login-links");
    menuLoginLinks.classList.toggle("responsive");
    document.body.classList.toggle("menu-open");
  });
}

function closeMenu(){
  document.addEventListener("click", function (event) {
    const menuLoginLinks = document.querySelector(".login-links");
    if (!menuLoginLinks) return;
    if (!menuLoginLinks.contains(event.target) && !event.target.closest('.mobile-menu-btn')) {
      menuLoginLinks.classList.remove("responsive");
      document.body.classList.remove("menu-open");
    }
  });
}

export {
  handleNavOnScroll,
  mobileMenuToggle,
  closeMenu,
};
