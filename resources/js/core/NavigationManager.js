/**
 * NavigationManager - Handles navigation behavior
 * Manages scroll effects and mobile menu
 */

class NavigationManager {
    constructor() {
      this.scrollThreshold = 50;
      this.menuOpen = false;
    }
  
    /**
     * Initialize navigation features
     */
    init() {
      this.handleScrollEffects();
      this.handleMobileMenu();
      this.handleMenuClose();
      return true;
    }
  
    /**
     * Handle navigation scroll effects
     */
    handleScrollEffects() {
      const nav = document.querySelector('.main-nav');
      if (!nav) return;
  
      window.addEventListener('scroll', () => {
        if (window.scrollY > this.scrollThreshold) {
          nav.classList.add('scrolled');
        } else {
          nav.classList.remove('scrolled');
        }
      });
    }
  
    /**
     * Handle mobile menu toggle
     */
    handleMobileMenu() {
      const menuBtn = document.querySelector('.mobile-menu-btn');
      if (!menuBtn) return;
  
      menuBtn.addEventListener('click', () => {
        this.toggleMenu();
      });
    }
  
    /**
     * Toggle mobile menu
     */
    toggleMenu() {
      const menuLoginLinks = document.querySelector('.login-links');
      if (!menuLoginLinks) return;
  
      this.menuOpen = !this.menuOpen;
      menuLoginLinks.classList.toggle('responsive');
      document.body.classList.toggle('menu-open');
    }
  
    /**
     * Handle menu close on outside click
     */
    handleMenuClose() {
      document.addEventListener('click', (event) => {
        const menuLoginLinks = document.querySelector('.login-links');
        if (!menuLoginLinks) return;
  
        if (!menuLoginLinks.contains(event.target) && 
            !event.target.closest('.mobile-menu-btn')) {
          menuLoginLinks.classList.remove('responsive');
          document.body.classList.remove('menu-open');
          this.menuOpen = false;
        }
      });
    }
  
    /**
     * Close menu programmatically
     */
    closeMenu() {
      const menuLoginLinks = document.querySelector('.login-links');
      if (menuLoginLinks) {
        menuLoginLinks.classList.remove('responsive');
        document.body.classList.remove('menu-open');
        this.menuOpen = false;
      }
    }
  
    /**
     * Check if menu is open
     * @returns {boolean}
     */
    isMenuOpen() {
      return this.menuOpen;
    }
  
    /**
     * Destroy and cleanup
     */
    destroy() {
      this.closeMenu();
    }
  }
  
  export default NavigationManager;