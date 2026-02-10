/**
 * PopoverManager - Manages popover elements
 * Handles native popover API with proper aria attributes
 */

class PopoverManager {
    constructor() {
      this.popovers = new Map();
    }
  
    /**
     * Initialize all popovers
     */
    init() {
      const popoverElements = document.querySelectorAll('.popover');
      
      popoverElements.forEach(popover => {
        this.setupPopover(popover);
      });
  
      return popoverElements.length > 0;
    }
  
    /**
     * Setup individual popover
     * @param {HTMLElement} popover - Popover element
     */
    setupPopover(popover) {
      const popoverId = popover.id;
      if (!popoverId) {
        console.warn('Popover missing ID:', popover);
        return;
      }
  
      const trigger = document.querySelector(`[aria-controls="${popoverId}"]`);
      if (!trigger) {
        console.warn(`No trigger found for popover: ${popoverId}`);
        return;
      }
  
      // Store reference
      this.popovers.set(popoverId, { popover, trigger });
  
      // Handle toggle events
      popover.addEventListener('toggle', (event) => {
        this.handleToggle(event, popoverId, trigger);
      });
    }
  
    /**
     * Handle popover toggle
     * @param {Event} event - Toggle event
     * @param {string} popoverId - Popover ID
     * @param {HTMLElement} trigger - Trigger element
     */
    handleToggle(event, popoverId, trigger) {
      const isOpen = event.target.matches(':popover-open');
      
      // Update aria attributes
      trigger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
  
      // Special handling for specific popovers
      if (popoverId === 'comment-section') {
        document.body.classList.toggle('always-no-scroll', isOpen);
      }
  
      // Trigger custom event
      const customEvent = new CustomEvent('popover:toggle', {
        detail: { popoverId, isOpen },
      });
      document.dispatchEvent(customEvent);
    }
  
    /**
     * Open a popover programmatically
     * @param {string} popoverId - Popover ID
     */
    open(popoverId) {
      const popoverData = this.popovers.get(popoverId);
      if (!popoverData) return;
  
      popoverData.popover.showPopover();
    }
  
    /**
     * Close a popover programmatically
     * @param {string} popoverId - Popover ID
     */
    close(popoverId) {
      const popoverData = this.popovers.get(popoverId);
      if (!popoverData) return;
  
      popoverData.popover.hidePopover();
    }
  
    /**
     * Toggle a popover programmatically
     * @param {string} popoverId - Popover ID
     */
    toggle(popoverId) {
      const popoverData = this.popovers.get(popoverId);
      if (!popoverData) return;
  
      popoverData.popover.togglePopover();
    }
  
    /**
     * Check if popover is open
     * @param {string} popoverId - Popover ID
     * @returns {boolean}
     */
    isOpen(popoverId) {
      const popoverData = this.popovers.get(popoverId);
      if (!popoverData) return false;
  
      return popoverData.popover.matches(':popover-open');
    }
  
    /**
     * Destroy and cleanup
     */
    destroy() {
      this.popovers.clear();
    }
  }
  
  export default PopoverManager;