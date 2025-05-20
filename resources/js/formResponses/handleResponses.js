export {
  handleResponse,
  handleGeneralErrors,
};

import {
  createNode,
  closeModal
} from '../helpers.js';

function handleResponse(result, form, actionUrl){
  const actionType = form.dataset.action;

  handleProfileImg(result, form, actionType);
  handleUser(result, form, actionType);
  handleAddBrand(result, form, actionUrl)
  showFlashMessage(result, result.message);
}

function handleGeneralErrors(result, form) {
  form.querySelectorAll('.error').forEach(el => el.remove());
  form.querySelectorAll('.error-message').forEach(el => el.textContent='');
  if (result.errors) {
    console.log(result.errors)
    Object.entries(result.errors).forEach(([fieldName, message]) => {
      const field = form.querySelector(`[name="${fieldName}"]`);
      if (!field) return; 
      const errorElement = createNode('span', message, null, 'error-message');
      field.classList.add('.error');
      field.insertAdjacentElement('afterend', errorElement);
    });
  } else if (result.success && form.dataset.submission === 'true') {
    form.reset();
    closeModal(result, form);
  }
}

function handleProfileImg(result, form, actionType){
  if (actionType === 'change-profile-image') {
    const profile = document.querySelector('.main-nav .avatar');

    if (result.profile_image_url && profile) {
      profile.style.backgroundImage = `url("${result.profile_image_url}")`;
      profile.textContent = ``
      profile.style.backgroundColor = ''
      profile.classList.replace('letter', 'img')

    } else if (result.profile_image_url === null) {
      profile.style.backgroundImage = ``;
      profile.classList.replace('img', 'letter')
      profile.style.backgroundColor = 'lightcoral'
      profile.textContent = `${result.email_initial}`
    } 
  }
}

function handleAddBrand(result, form, actionUrl){
  if (result.success && actionUrl === '/brands/complete') {
    form.reset();
    closeModal(result, form);
  }

  if (result.success) {
    const fieldsets = form.querySelectorAll('.add-brand-modal fieldset');
    const current = form.querySelector('.add-brand-modal fieldset.active');

    if (!current) {
      console.warn('No active fieldset found.');
      return;
    }

    const currentIndex = Array.from(fieldsets).indexOf(current);
    if (currentIndex >= 0 && currentIndex < fieldsets.length - 1) {
      fieldsets[currentIndex].classList.remove('active');
      fieldsets[currentIndex + 1].classList.add('active');
    }
  }
}

function handleUser(result, form, actionType){
  if (actionType === 'change-username') {
    const user = document.querySelector('.current-username');
    if (!user) console.warn('.current-username not found');
    user.textContent = `Username : ${result.username}`;

    const usernameInput = form.querySelector('#change-username');
    if (!usernameInput) console.warn('#username input not found');
    usernameInput.value = result.username;
  }
}

function showFlashMessage(result, message) {
  if (result.message) {
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
    closeFlashMessages();
  }
}
  
function closeFlashMessages(){
  const closeFlash = document.querySelectorAll('.flash-message i')
  closeFlash.forEach(function(close) {
    close.addEventListener('click', function(event){
      const closestFlash = event.target.closest('.flash-message')
      if(closestFlash) closestFlash.remove();
    })
  })
}
closeFlashMessages();