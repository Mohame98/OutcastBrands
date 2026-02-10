/**
 * SliderManager - Manages all carousel/slider instances
 * Wraps Slick slider library for better control and cleanup
 */

class SliderManager {
    constructor() {
      this.sliders = new Map();
    }
  
    /**
     * Initialize all sliders
     */
    init() {
      this.initBrandImageSlider();
      this.initBrandMediaPreview();
      return this.sliders.size > 0;
    }
  
    /**
     * Initialize brand image slider
     * Matches selector: .brand-image-slider
     */
    initBrandImageSlider() {
      const selector = '.brand-image-slider';
      const slider = document.querySelector(selector);
      if (!slider) return;
  
      // Check if jQuery and Slick are available
      if (typeof $ === 'undefined' || !$.fn.slick) {
        console.warn('Slick slider library not available');
        return;
      }
  
      const config = {
        fade: true,
        infinite: false,
        slidesToShow: 1,
        slidesToScroll: 1,
      };
  
      const $slider = $(selector).slick(config);
      this.sliders.set('brand-image-slider', { element: $slider, config });
    }
  
    /**
     * Initialize brand media preview slider
     * Matches selector: .media-preview.brand
     */
    initBrandMediaPreview() {
      const selector = '.media-preview.brand';
      const preview = document.querySelector(selector);
      if (!preview) return;
  
      // Check if jQuery and Slick are available
      if (typeof $ === 'undefined' || !$.fn.slick) {
        console.warn('Slick slider library not available');
        return;
      }
  
      const config = {
        fade: true,
        infinite: false,
        slidesToShow: 1,
        slidesToScroll: 1,
      };
  
      const $preview = $(selector).slick(config);
      this.sliders.set('media-preview', { element: $preview, config });
    }
  
    /**
     * Get a slider instance by name
     * @param {string} name - Slider name ('brand-image-slider' or 'media-preview')
     * @returns {jQuery|null}
     */
    getSlider(name) {
      const slider = this.sliders.get(name);
      return slider ? slider.element : null;
    }
  
    /**
     * Add a slide to a slider
     * @param {string} sliderName - Slider name
     * @param {string} slideHTML - Slide HTML
     * @param {number} index - Position to insert (optional)
     */
    addSlide(sliderName, slideHTML, index = null) {
      const $slider = this.getSlider(sliderName);
      if (!$slider) return;
  
      if (index !== null) {
        $slider.slick('slickAdd', slideHTML, index);
      } else {
        $slider.slick('slickAdd', slideHTML);
      }
    }
  
    /**
     * Remove a slide from a slider
     * @param {string} sliderName - Slider name
     * @param {number} index - Slide index to remove
     */
    removeSlide(sliderName, index) {
      const $slider = this.getSlider(sliderName);
      if (!$slider) return;
  
      $slider.slick('slickRemove', index);
    }
  
    /**
     * Go to a specific slide
     * @param {string} sliderName - Slider name
     * @param {number} index - Slide index
     */
    goToSlide(sliderName, index) {
      const $slider = this.getSlider(sliderName);
      if (!$slider) return;
  
      $slider.slick('slickGoTo', index);
    }
  
    /**
     * Get current slide index
     * @param {string} sliderName - Slider name
     * @returns {number}
     */
    getCurrentSlide(sliderName) {
      const $slider = this.getSlider(sliderName);
      if (!$slider) return -1;
  
      return $slider.slick('slickCurrentSlide');
    }
  
    /**
     * Play slider (if autoplay enabled)
     * @param {string} sliderName - Slider name
     */
    play(sliderName) {
      const $slider = this.getSlider(sliderName);
      if (!$slider) return;
  
      $slider.slick('slickPlay');
    }
  
    /**
     * Pause slider
     * @param {string} sliderName - Slider name
     */
    pause(sliderName) {
      const $slider = this.getSlider(sliderName);
      if (!$slider) return;
  
      $slider.slick('slickPause');
    }
  
    /**
     * Go to next slide
     * @param {string} sliderName - Slider name
     */
    next(sliderName) {
      const $slider = this.getSlider(sliderName);
      if (!$slider) return;
  
      $slider.slick('slickNext');
    }
  
    /**
     * Go to previous slide
     * @param {string} sliderName - Slider name
     */
    prev(sliderName) {
      const $slider = this.getSlider(sliderName);
      if (!$slider) return;
  
      $slider.slick('slickPrev');
    }
  
    /**
     * Refresh/reinitialize a slider
     * @param {string} sliderName - Slider name
     */
    refresh(sliderName) {
      const slider = this.sliders.get(sliderName);
      if (!slider) return;
  
      slider.element.slick('refresh');
    }
  
    /**
     * Destroy a specific slider
     * @param {string} name - Slider name
     */
    destroySlider(name) {
      const slider = this.sliders.get(name);
      if (!slider) return;
  
      slider.element.slick('unslick');
      this.sliders.delete(name);
    }
  
    /**
     * Destroy all sliders
     */
    destroy() {
      this.sliders.forEach((slider, name) => {
        this.destroySlider(name);
      });
    }
  }
  
  export default SliderManager;