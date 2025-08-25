function setupPasswordToggles() {
  const toggleButtons = document.querySelectorAll(".toggle-password");
  if (toggleButtons) {
    toggleButtons.forEach(button => {
      button.addEventListener("click", function () {
        const wrapper = button.closest(".password-field");
        const passwordInput = wrapper.querySelector(".password-input");
        const icon = button.querySelector('i');
        const caption = button.querySelector('.hover-caption');
        const isPassword = passwordInput.type === "password";

        passwordInput.type = isPassword ? "text" : "password";
        icon?.classList.toggle("fa-eye");
        icon?.classList.toggle("fa-eye-slash");
        if (caption) caption.textContent = isPassword ? "Hide" : "Show";
    
        button.setAttribute("aria-pressed", isPassword ? "true" : "false");
        button.setAttribute("aria-label", isPassword ? "Hide password" : "Show password");
      });
    });
  }
}

function createNode(type, text, parentNode, className, id, href, attributes = {}) {
  let node = document.createElement(type);  
  if (text && type !== "img") node.appendChild(document.createTextNode(text));
  if (className) node.className = className;
  if (id) node.id = id;
  if (href) node.href = href;
  if (type === "img") node.src = text;
  for (let [key, value] of Object.entries(attributes)) {
    node.setAttribute(key, value);
  }
  if (parentNode) parentNode.appendChild(node);
  return node;
}

function handleSpecificError(message, field){
  const errorEl = document.querySelector(`#error-${field}`);
  if (errorEl) {
    errorEl.textContent = `${message}`;
    errorEl.style.display = 'block';
    errorEl.style.color = 'red';
    errorEl.style.fontSize = '0.75rem';
  }
}

function clearSpecificError(field) {
  const errorElements = document.querySelectorAll(`#error-${field}`);
  if (!errorElements) return;
  errorElements.forEach(el => {
    el.textContent = '';
    el.style.display = 'none';
  });
}

function closeModal(result, form){
  const dialog = form.closest('dialog');
  if (!result.success) return; 
  form.reset(); 
  if (!dialog) return;
  dialog.close();
  if (!document.body.classList.contains('no-scroll')) {
    document.body.classList.remove('no-scroll');
  }
}

function selectOnlyThreeCategories() {
  const container = document.querySelector('.category-list');
  if (!container) return;
  const checkboxes = container.querySelectorAll('.category-checkbox');
  const maxAllowed = parseInt(container.dataset.limit || 3);

  function updateCheckboxStates() {
    const checkedCount = container.querySelectorAll('.category-checkbox:checked').length;
    if (checkedCount >= maxAllowed) {
      checkboxes.forEach(cb => {
        if (!cb.checked) cb.disabled = true;
      });
    } else {
      checkboxes.forEach(cb => cb.disabled = false);
    }
  }
  checkboxes.forEach(checkbox => {
    checkbox.addEventListener('change', updateCheckboxStates);
  });
  updateCheckboxStates();
}

function moveBackSteps(form) {
  const fieldsets = form.querySelectorAll('.multi-field');
  const current = form.querySelector('.multi-field.active');

  if (!current) {
    console.warn('No active fieldset found.');
    return;
  }

  const currentIndex = Array.from(fieldsets).indexOf(current);
  if (currentIndex > 0) {
    fieldsets[currentIndex].classList.remove('active');
    fieldsets[currentIndex - 1].classList.add('active');
  }
}

function backButtons() {
  document.body.addEventListener('click', function(e) {
    const button = e.target.closest('[data-back-button]');
    if (!button) return;

    const form = button.closest('form');
    if (!form) return;
    moveBackSteps(form);
  });
}

function trimInputs(){
  document.querySelectorAll('input, textarea').forEach(input => {
    input.value = input.value.trim();
  });
}
     
export {
  createNode,
  setupPasswordToggles,
  handleSpecificError,
  selectOnlyThreeCategories,
  closeModal,
  backButtons,
  clearSpecificError,
  trimInputs,
};