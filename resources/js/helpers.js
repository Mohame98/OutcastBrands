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
  const errorEl = document.querySelector(`.error-${field}`);
  if (errorEl) {
    errorEl.textContent = `${message}`;
    errorEl.style.display = 'block';
  }
}

function closeModal(result, form){
  const dialog = form.closest('dialog');
  if (result.success && dialog) {
    dialog.close();
  }
}

function closeDetails(){
  document.addEventListener('click', function(event) {
    document.querySelectorAll('details[open]').forEach((dropdown) => {
      if (!dropdown.contains(event.target)) {
        dropdown.removeAttribute('open');
      }
    });
  });
}

export {
  createNode,
  setupPasswordToggles,
  handleSpecificError,
  closeModal,
  closeDetails,
};