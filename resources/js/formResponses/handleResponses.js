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
  trimAllFields(form);

  handleProfileImg(result, form, actionType);
  handleUser(result, actionType);

  handleVoteCount(result, actionType);
  handleSave(result, actionType);

  handleSingleInput(form, actionType, '#change-username', result.username, "change-username");
  handleSingleInput(form, actionType, '#bio', result.bio, "change-bio");
  handleSingleInput(form, actionType, '#instagram', result.instagram, "change-instagram");
  handleSingleInput(form, actionType, '#user_location', result.user_location, "change-location");

  handleStepFormSubmissions(result, form, actionType, actionUrl, '/add-brands/step-3', "add-brand");
  handleStepFormSubmissions(result, form, actionType, actionUrl, '/report-brand/step-2', "report");

  showFlashMessage(result, result.message);
  moveSteps(result, form);
}

function handleGeneralErrors(result, form) {
  form.querySelectorAll('.error').forEach(el => el.classList.remove('error'));
  form.querySelectorAll('.error-message').forEach(el => el.textContent='');

  if (result.errors || result.error) {
    if (result.error) {
      const errorElement = createNode('span', result.error, null, 'error-message');
      form.insertAdjacentElement('afterbegin', errorElement);
    }

    if (result.errors) {
      Object.entries(result.errors).forEach(([fieldName, messages]) => {
        const arrayField = form.querySelector(`[name="${fieldName}[]"]`);
        const field = form.querySelector(`[name="${fieldName}"]`);

        if (arrayField){
          const selector = arrayField.dataset.selector
          const container = document.querySelector(`.${selector}`);
          const errorElement = createNode('span', messages, null, 'error-message');
          arrayField.classList.add('error');
          container.insertAdjacentElement('afterend', errorElement);
        }

        if (field){
          const label = form.querySelector(`.${field.name}`);
          const errorElement = createNode('span', messages[0], null, 'error-message');
          field.classList.add('error');
          label.insertAdjacentElement('afterend', errorElement);
        } 
      });
    }
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
      profile.textContent = ``;
      profile.style.backgroundColor = '';
      profile.classList.replace('letter', 'img');

    } else if (result.profile_image_url === null) {
      profile.style.backgroundImage = ``;
      profile.classList.replace('img', 'letter');
      profile.style.backgroundColor = 'lightcoral';
      profile.textContent = result.email_initial;
    } 
  }
}

function handleStepFormSubmissions(result, form, actionType, actionUrl, expectedUrl, type){
  if (actionType === type) {
    if (result.success && actionUrl === expectedUrl) {
      form.reset();
      closeModal(result, form);
    }
  }
}

function handleVoteCount(result, actionType){
  if (actionType === 'vote') {
    const voteCount = document.querySelector('.vote-count');
    voteCount.textContent = result.total_votes
  }
}

function handleSave(result, actionType){
  if (actionType === 'save') {
    const saveBtn = document.querySelector('.save-btn');
    saveBtn.innerHTML = result.saved 
      ? '<i class="fa-solid fa-bookmark"></i> Saved' 
      : '<i class="fa-regular fa-bookmark"></i> Unsaved';
  }
}

function handleUser(result, actionType){
  if (actionType === 'change-username') {
    const user = document.querySelector('.current-username');
    if (!user) {
      console.warn('.current-username not found');
      return;
    }
    user.textContent = `Username : ${result.username}`;
  }
}

function handleSingleInput(form, actionType, selector, value, type){
  if (actionType === type) {
    const input = form.querySelector(selector);
    if (!input) {
      console.warn(`${selector} is not found`);
      return;
    }
    input.value = value;
  }
}

function showFlashMessage(result, message) {
  if (result.message) {
    document.querySelectorAll('.flash-message').forEach(msg => msg.remove());
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

function moveSteps(result, form){
   if (result.success && result.multi_step) {
    const fieldsets = form.querySelectorAll('.multi-field');
    const current = form.querySelector('.multi-field.active');

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

function trimAllFields(form){
  form.querySelectorAll('input, textarea').forEach(el => {
    el.value = el.value.trim();
  });
}