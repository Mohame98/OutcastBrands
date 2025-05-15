import { handleModals } from './modals.js';
import { handleMedia } from './mediaInputs.js';

async function handleFormSubmissions(event) {
  event.preventDefault();
  const form = event.target.closest('form'); 
  if (!form) return;
  const formData = new FormData(form);
  const formMethod = form.method;
  const actionUrl = form.action;
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
  await sendForm(actionUrl, formMethod, form, formData, csrfToken);
}

async function sendForm(actionUrl, formMethod, form, formData, csrfToken) {
  const options = {
    method: formMethod,
    body: formData,
    headers: {
      'X-CSRF-TOKEN': csrfToken,
      'Accept': 'application/json'
    }
  };
  await handleRequest(actionUrl, options, form);
}
    
async function handleRequest(actionUrl, options, form){
  try {
    const response = await fetch(actionUrl, options);
    if (!response.ok) console.error('Error:', 'Unknown error');

    const result = await response.json();
    if (result) handleResponse(result, form);
  } catch (error) {
    console.error('Network Error:', error);
  }
}

function handleResponse(result, form) {
  const actionType = form.dataset.action;
  if (result.success) {
    clearValidationErrors(form);
    closeModal(result, form);
  }

  
  handleProfileImg(result, form, actionType);
  handleUser(result, form, actionType);
  handlePass(result, form, actionType);



  showFlashMessage(result.message);
}

function handleProfileImg(result, form, actionType){
  if (actionType === 'change-profile-image') {
    const profile = document.querySelector('#profile');
    if (result.profile_image_url) {
      profile.style.backgroundImage = `url("${result.profile_image_url}")`;
      profile.textContent = ''
    } else {
      profile.style.backgroundImage = `url("")`;
    }
  }
}

function closeModal(result, form){
  const dialog = form.closest('dialog');
  if (result.success && dialog) {
    dialog.close();
  }
}

function handleUser(result, form, actionType){
  if (actionType === 'change-username') {
    const user = document.querySelector('.current-username');
    user.textContent = `Username : ${result.username}`
  }
}

function handlePass(result, form, actionType){
  if (actionType === 'change-password') {
    handleError(result.message, 'delete_current_password');
  }
}

function showFlashMessage(message) {
  const flashMessageContainer = createNode('section', null, document.body, 'flash-message', null, null, {
    'data-action': 'flash-message',
    'role': 'alert',
    'aria-live': 'assertive',
  });

  const innerDiv = createNode('div');
  createNode('i', null, innerDiv, 'fa-solid fa-info'); 
  createNode('p', message, innerDiv, 'flash-text');
  flashMessageContainer.appendChild(innerDiv);
  createNode('i', null, flashMessageContainer, 'fa-solid fa-xmark');
  closeFlashMessages()
}

function closeFlashMessages(){
  const closeFlash = document.querySelectorAll('.flash-message i')
  closeFlash.forEach(function(close) {
    close.addEventListener('click', function(event){
      const closestFlash = event.target.closest('.flash-message')
      if(closestFlash) closestFlash.style.display = "none"
    })
  })
}
closeFlashMessages()

function clearValidationErrors(form) {
  const errorEls = form.querySelectorAll('.error');
  if (errorEls) {
    errorEls.forEach(el => {
      el.textContent = '';
      el.style.display = 'none';
    });
  }
}

function main() {
  document.body.addEventListener('submit', function(event) {
    if (event.target && event.target.matches('.action-form')) {
      handleFormSubmissions(event);  
    }
  });
  handleModals();
  handleMedia();
}
main();

function userValidation(){
  const input = document.querySelector('#change-username');
  const charCount = document.querySelector('#charCount');
  const errorMessage = document.querySelector('.error-username');
  const submitBtn = document.querySelector('.user .update');

  const maxLength = 90;
  const usernameRegex = /^[a-zA-Z0-9._-]+$/;

  if (input) {
    function remainingCharacters() {
      const value = input.value;
      const remaining = maxLength - value.length;
      charCount.textContent = `${Math.max(remaining, 0)} Remaining`;

      if (remaining < 0) {
        handleError('Submission blocked: input exceeds 90 characters.', 'username');
        submitBtn.disabled = true;
        return;
      }

      if (value && !usernameRegex.test(value)) {
        handleError('letters, numbers, dots, underscores, and hyphens.', 'username');
        submitBtn.disabled = true;
        return;
      }
      errorMessage.style.display = 'none';
      submitBtn.disabled = false;
    }
    input.addEventListener('input', remainingCharacters);
    remainingCharacters();
  }
}
userValidation();

function handleError(message, field){
  const errorEl = document.querySelector(`.error-${field}`);
  if (errorEl) {
    errorEl.textContent = `${message}`;
    errorEl.style.display = 'block';
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