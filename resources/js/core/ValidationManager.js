/**
 * ValidationManager - Versatile input validation system
 * Can be attached to ANY input with flexible validation rules
 * 
 * Usage:
 *   const validator = new ValidationManager();
 *   validator.addRule('username', {
 *     maxLength: 90,
 *     pattern: /^[a-zA-Z0-9._-]+$/,
 *     messages: { ... }
 *   });
 */

class ValidationManager {
    constructor() {
      this.rules = new Map();
      this.validators = new Map();
      this.errorElements = new Map();
      
      // Built-in validation patterns
      this.patterns = {
        email: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
        username: /^[a-zA-Z0-9._-]+$/,
        alphanumeric: /^[a-zA-Z0-9]+$/,
        alpha: /^[a-zA-Z]+$/,
        numeric: /^[0-9]+$/,
        url: /^https?:\/\/.+/,
        phone: /^\+?[\d\s\-()]+$/,
      };
    }
  
    /**
     * Initialize validation system
     */
    init() {
      this.setupUsernameValidation(); // Your specific validation
      this.attachGlobalTrimming(); // Trim inputs on blur
      return true;
    }
  
    /**
     * Add a validation rule to an input
     * @param {string} inputSelector - CSS selector for input
     * @param {Object} config - Validation configuration
     * 
     * config = {
     *   required: boolean,
     *   minLength: number,
     *   maxLength: number,
     *   pattern: RegExp | string (key from this.patterns),
     *   min: number (for numeric inputs),
     *   max: number (for numeric inputs),
     *   custom: function(value) { return {valid: boolean, message: string} },
     *   messages: {
     *     required: 'Custom required message',
     *     minLength: 'Custom min length message',
     *     maxLength: 'Custom max length message',
     *     pattern: 'Custom pattern message',
     *     custom: 'Custom validation message'
     *   },
     *   liveValidation: boolean (default: true),
     *   showCharCount: boolean,
     *   charCountSelector: string,
     *   errorSelector: string,
     *   submitButtonSelector: string,
     *   trim: boolean (default: true)
     * }
     */
    addRule(inputSelector, config) {
      const input = document.querySelector(inputSelector);
      if (!input) {
        console.warn(`Input not found: ${inputSelector}`);
        return;
      }
  
      // Store rule
      this.rules.set(inputSelector, config);
  
      // Create validator function
      const validator = (value) => this.validateValue(value, config);
      this.validators.set(inputSelector, validator);
  
      // Setup error element
      const errorSelector = config.errorSelector || `#error-${input.name || input.id}`;
      const errorElement = document.querySelector(errorSelector);
      if (errorElement) {
        this.errorElements.set(inputSelector, errorElement);
      }
  
      // Setup character counter if needed
      if (config.showCharCount && config.charCountSelector) {
        this.setupCharCounter(input, config);
      }
  
      // Attach event listeners
      if (config.liveValidation !== false) {
        input.addEventListener('input', () => {
          this.validateInput(inputSelector);
        });
  
        input.addEventListener('blur', () => {
          if (config.trim !== false) {
            input.value = input.value.trim();
          }
          this.validateInput(inputSelector);
        });
      }
  
      // Initial validation
      this.validateInput(inputSelector);
    }
  
    /**
     * Validate a value against config rules
     * @param {string} value - Value to validate
     * @param {Object} config - Validation config
     * @returns {Object} {valid: boolean, message: string}
     */
    validateValue(value, config) {
      const messages = config.messages || {};
  
      // Required check
      if (config.required && !value) {
        return {
          valid: false,
          message: messages.required || 'This field is required.',
        };
      }
  
      // Skip other validations if empty and not required
      if (!value && !config.required) {
        return { valid: true };
      }
  
      // Min length
      if (config.minLength && value.length < config.minLength) {
        return {
          valid: false,
          message: messages.minLength || `Minimum ${config.minLength} characters required.`,
        };
      }
  
      // Max length
      if (config.maxLength && value.length > config.maxLength) {
        return {
          valid: false,
          message: messages.maxLength || `Maximum ${config.maxLength} characters allowed.`,
        };
      }
  
      // Min value (for numbers)
      if (config.min !== undefined && parseFloat(value) < config.min) {
        return {
          valid: false,
          message: messages.min || `Minimum value is ${config.min}.`,
        };
      }
  
      // Max value (for numbers)
      if (config.max !== undefined && parseFloat(value) > config.max) {
        return {
          valid: false,
          message: messages.max || `Maximum value is ${config.max}.`,
        };
      }
  
      // Pattern validation
      if (config.pattern) {
        const pattern = typeof config.pattern === 'string' 
          ? this.patterns[config.pattern] 
          : config.pattern;
  
        if (pattern && !pattern.test(value)) {
          return {
            valid: false,
            message: messages.pattern || 'Invalid format.',
          };
        }
      }
  
      // Custom validation
      if (config.custom && typeof config.custom === 'function') {
        const result = config.custom(value);
        if (!result.valid) {
          return {
            valid: false,
            message: messages.custom || result.message || 'Validation failed.',
          };
        }
      }
  
      return { valid: true };
    }
  
    /**
     * Validate an input
     * @param {string} inputSelector - Input selector
     * @returns {boolean} Whether input is valid
     */
    validateInput(inputSelector) {
      const input = document.querySelector(inputSelector);
      const validator = this.validators.get(inputSelector);
      const config = this.rules.get(inputSelector);
      
      if (!input || !validator) return true;
  
      const result = validator(input.value);
      
      // Update UI
      this.updateInputState(inputSelector, result);
      
      // Update submit button if specified
      if (config.submitButtonSelector) {
        const submitBtn = document.querySelector(config.submitButtonSelector);
        if (submitBtn) {
          submitBtn.disabled = !result.valid;
        }
      }
  
      return result.valid;
    }
  
    /**
     * Update input state (error messages, classes)
     * @param {string} inputSelector - Input selector
     * @param {Object} result - Validation result
     */
    updateInputState(inputSelector, result) {
      const input = document.querySelector(inputSelector);
      const errorElement = this.errorElements.get(inputSelector);
  
      if (!input) return;
  
      // Update input class
      if (result.valid) {
        input.classList.remove('error', 'invalid');
        input.classList.add('valid');
      } else {
        input.classList.remove('valid');
        input.classList.add('error', 'invalid');
      }
  
      // Update error message
      if (errorElement) {
        if (result.valid) {
          errorElement.textContent = '';
          errorElement.style.display = 'none';
        } else {
          errorElement.textContent = result.message;
          errorElement.style.display = 'block';
          errorElement.style.color = 'red';
          errorElement.style.fontSize = '0.75rem';
        }
      }
    }
  
    /**
     * Setup character counter
     * @param {HTMLElement} input - Input element
     * @param {Object} config - Config with maxLength and charCountSelector
     */
    setupCharCounter(input, config) {
      const charCountEl = document.querySelector(config.charCountSelector);
      if (!charCountEl) return;
  
      const updateCount = () => {
        const remaining = config.maxLength - input.value.length;
        charCountEl.textContent = `${Math.max(remaining, 0)} Remaining`;
        
        if (remaining < 0) {
          charCountEl.style.color = 'red';
        } else if (remaining < 10) {
          charCountEl.style.color = 'orange';
        } else {
          charCountEl.style.color = '';
        }
      };
  
      input.addEventListener('input', updateCount);
      updateCount();
    }
  
    /**
     * Setup username validation (your specific use case)
     */
    setupUsernameValidation() {
      const input = document.querySelector('#username');
      if (!input) return;
  
      this.addRule('#username', {
        maxLength: 90,
        pattern: 'username',
        messages: {
          maxLength: 'Submission blocked: input exceeds 90 characters.',
          pattern: 'Only letters, numbers, dots, underscores, and hyphens allowed.',
        },
        showCharCount: true,
        charCountSelector: '#charCount',
        errorSelector: '.error-username',
        submitButtonSelector: '.user .update',
        liveValidation: true,
        trim: true,
      });
    }
  
    /**
     * Validate all registered inputs
     * @returns {boolean} Whether all inputs are valid
     */
    validateAll() {
      let allValid = true;
      
      this.rules.forEach((config, selector) => {
        const isValid = this.validateInput(selector);
        if (!isValid) allValid = false;
      });
  
      return allValid;
    }
  
    /**
     * Reset validation for an input
     * @param {string} inputSelector - Input selector
     */
    reset(inputSelector) {
      const input = document.querySelector(inputSelector);
      const errorElement = this.errorElements.get(inputSelector);
  
      if (input) {
        input.classList.remove('error', 'invalid', 'valid');
        input.value = '';
      }
  
      if (errorElement) {
        errorElement.textContent = '';
        errorElement.style.display = 'none';
      }
    }
  
    /**
     * Reset all validations
     */
    resetAll() {
      this.rules.forEach((config, selector) => {
        this.reset(selector);
      });
    }
  
    /**
     * Attach global input trimming
     */
    attachGlobalTrimming() {
      document.querySelectorAll('input, textarea').forEach(input => {
        input.addEventListener('blur', () => {
          input.value = input.value.trim();
        });
      });
    }
  
    /**
     * Remove a validation rule
     * @param {string} inputSelector - Input selector
     */
    removeRule(inputSelector) {
      this.rules.delete(inputSelector);
      this.validators.delete(inputSelector);
      this.errorElements.delete(inputSelector);
    }
  
    /**
     * Get validation config for an input
     * @param {string} inputSelector - Input selector
     * @returns {Object|null}
     */
    getRule(inputSelector) {
      return this.rules.get(inputSelector) || null;
    }
  }
  
  export default ValidationManager;