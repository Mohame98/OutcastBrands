/**
 * MultiStepFormManager - Manages multi-step forms
 * Handles navigation between steps, validation, and progress tracking
 */
class MultiStepFormManager {
  constructor(formSelector = null) {
    this.formSelector = formSelector;
    this.forms = new Map();
  }

  /**
   * Initialize multi-step forms
   */
  init() {
    if (this.formSelector) {
      // Initialize specific form
      const form = document.querySelector(this.formSelector);
      if (form) this.setupForm(form);
    } else {
      // Initialize all forms with multi-step fields
      const forms = document.querySelectorAll("form");
      forms.forEach((form) => {
        if (form.querySelector(".multi-field")) {
          this.setupForm(form);
        }
      });
    }

    this.attachBackButtonListener();
    return this.forms.size > 0;
  }

  /**
   * Setup individual form (syncs current step from DOM if already visible)
   * @param {HTMLFormElement} form - Form element
   */
  setupForm(form) {
    const fieldsets = form.querySelectorAll(".multi-field");
    if (fieldsets.length === 0) return;

    if (this.getFormId(form)) return;

    const formId = form.id || `form-${this.forms.size}`;
    const fieldsetsArray = Array.from(fieldsets);

    let currentStep = 0;
    const activeFieldset = form.querySelector(".multi-field.active");
    if (activeFieldset) {
      const idx = fieldsetsArray.indexOf(activeFieldset);
      if (idx >= 0) currentStep = idx;
    }

    this.forms.set(formId, {
      form,
      fieldsets: fieldsetsArray,
      currentStep,
      totalSteps: fieldsetsArray.length,
    });

    this.updateProgress(formId);
  }

  /**
   * Go to a specific step
   * @param {string} formId - Form ID
   * @param {number} stepIndex - Step index (0-based)
   * @param {boolean} validate - Whether to validate current step
   * @returns {boolean} Whether navigation was successful
   */
  goToStep(formId, stepIndex, validate = false) {
    const formData = this.forms.get(formId);
    if (!formData) return false;

    const { fieldsets, currentStep } = formData;

    // Validate current step if required
    if (validate && !this.validateStep(formId, currentStep)) {
      return false;
    }

    // Check if step index is valid
    if (stepIndex < 0 || stepIndex >= fieldsets.length) {
      return false;
    }

    // Remove active class from all fieldsets
    fieldsets.forEach((fieldset) => {
      fieldset.classList.remove("active");
    });

    // Add active class to target fieldset
    fieldsets[stepIndex].classList.add("active");

    // Update current step
    formData.currentStep = stepIndex;

    // Update progress
    this.updateProgress(formId);

    // Trigger custom event
    const event = new CustomEvent("step:changed", {
      detail: { formId, step: stepIndex, totalSteps: fieldsets.length },
    });
    formData.form.dispatchEvent(event);

    // Focus first input in new step
    const firstInput = fieldsets[stepIndex].querySelector(
      "input, textarea, select",
    );
    if (firstInput) {
      setTimeout(() => firstInput.focus(), 100);
    }

    return true;
  }

  /**
   * Go to next step
   * @param {string} formId - Form ID
   * @param {boolean} validate - Whether to validate current step
   * @returns {boolean} Whether navigation was successful
   */
  nextStep(formId, validate = true) {
    const formData = this.forms.get(formId);
    if (!formData) return false;

    const nextIndex = formData.currentStep + 1;
    return this.goToStep(formId, nextIndex, validate);
  }

  /**
   * Go to previous step
   * @param {string} formId - Form ID
   * @returns {boolean} Whether navigation was successful
   */
  previousStep(formId) {
    const formData = this.forms.get(formId);
    if (!formData) return false;

    const prevIndex = formData.currentStep - 1;
    return this.goToStep(formId, prevIndex, false);
  }

  /**
   * Validate current step
   * @param {string} formId - Form ID
   * @param {number} stepIndex - Step to validate
   * @returns {boolean} Whether step is valid
   */
  validateStep(formId, stepIndex) {
    const formData = this.forms.get(formId);
    if (!formData) return true;

    const fieldset = formData.fieldsets[stepIndex];
    if (!fieldset) return true;

    // Get all inputs in this step
    const inputs = fieldset.querySelectorAll("input, textarea, select");
    let isValid = true;

    inputs.forEach((input) => {
      // Check HTML5 validity
      if (!input.checkValidity()) {
        isValid = false;
        input.reportValidity();
      }

        // Check required fields
      if (input.hasAttribute("required") && !input.value.trim()) {
        isValid = false;
        input.classList.add("error");
      }
    });
    return isValid;
  }

  /**
   * Update progress indicator
   * @param {string} formId - Form ID
   */
  updateProgress(formId) {
    const formData = this.forms.get(formId);
    if (!formData) return;

    const { form, currentStep, totalSteps } = formData;

    // Update progress bar if exists
    const progressBar = form.querySelector(".progress-bar");
    if (progressBar) {
      const percentage = ((currentStep + 1) / totalSteps) * 100;
      progressBar.style.width = `${percentage}%`;
    }

    // Update step counter if exists
    const stepCounter = form.querySelector(".step-counter");
    if (stepCounter) {
      stepCounter.textContent = `Step ${currentStep + 1} of ${totalSteps}`;
    }

    // Update step indicators
    const indicators = form.querySelectorAll(".step-indicator");
    indicators.forEach((indicator, index) => {
      if (index < currentStep) {
        indicator.classList.add("completed");
        indicator.classList.remove("active");
      } else if (index === currentStep) {
        indicator.classList.add("active");
        indicator.classList.remove("completed");
      } else {
        indicator.classList.remove("active", "completed");
      }
    });
  }

  /**
   * Move to previous step (DOM-based, no registration required)
   * @param {HTMLFormElement} form - Form element
   */
  moveBackSteps(form) {
    const fieldsets = form.querySelectorAll(".multi-field");
    const current = form.querySelector(".multi-field.active");

    if (!current) return;

    const currentIndex = Array.from(fieldsets).indexOf(current);
    if (currentIndex > 0) {
      fieldsets[currentIndex].classList.remove("active");
      fieldsets[currentIndex - 1].classList.add("active");

      const formId = this.getFormId(form);
      if (formId) {
        const formData = this.forms.get(formId);
        if (formData) {
          formData.currentStep = currentIndex - 1;
          this.updateProgress(formId);
        }
      }
    }
  }

  /**
   * Attach global back button listener
   */
  attachBackButtonListener() {
    document.body.addEventListener("click", (e) => {
      const button = e.target.closest("[data-back-button]");
      if (!button) return;

      const form = button.closest("form");
      if (!form) return;

      e.preventDefault();
      this.moveBackSteps(form);
    });
  }

  /**
   * Get form ID from form element
   * @param {HTMLFormElement} form - Form element
   * @returns {string|null}
   */
  getFormId(form) {
    for (const [id, data] of this.forms) {
      if (data.form === form) return id;
    }
    return null;
  }

  /**
   * Check if on first step
   * @param {string} formId - Form ID
   * @returns {boolean}
   */
  isFirstStep(formId) {
    const formData = this.forms.get(formId);
    return formData ? formData.currentStep === 0 : false;
  }

  /**
   * Check if on last step
   * @param {string} formId - Form ID
   * @returns {boolean}
   */
  isLastStep(formId) {
    const formData = this.forms.get(formId);
    return formData
      ? formData.currentStep === formData.totalSteps - 1
      : false;
  }

  /**
   * Get current step
   * @param {string} formId - Form ID
   * @returns {number}
   */
  getCurrentStep(formId) {
    const formData = this.forms.get(formId);
    return formData ? formData.currentStep : -1;
  }

  /**
   * Get total steps
   * @param {string} formId - Form ID
   * @returns {number}
   */
  getTotalSteps(formId) {
    const formData = this.forms.get(formId);
    return formData ? formData.totalSteps : 0;
  }

  /**
   * Reset form to first step
   * @param {string} formId - Form ID
   */
  reset(formId) {
    this.goToStep(formId, 0, false);
  }

  /**
   * Destroy and cleanup
   */
  destroy() {
    this.forms.clear();
  }
}

export default MultiStepFormManager;
