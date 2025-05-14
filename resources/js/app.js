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
    clearValidationErrors(form)
  } catch (error) {
    console.error('Network Error:', error);
  }
}

function handleResponse(result, form) {
  const actionType = form.dataset.action;
  handleProfileImg(result, form, actionType);
  handleFlashMessages(result, actionType);
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
   
    if (result.success) {
      const dialog = form.closest('dialog');
      dialog.close();
    }
  }
}

function handleFlashMessages(result, actionType){
  if(actionType === 'change-profile-image'){
    const flash = document.querySelector('.message');
    flash.textContent = result.message;
    flash.style.display = 'block';
    flash.classList.add('success');
  }
}

// function closeFlashMessages(){
//     const closeFlash = document.querySelectorAll('.flash-message i')
//     closeFlash.forEach(function(close) {
//         close.addEventListener('click', function(event){
//             const closestFlash = event.target.closest('.flash-message')
//             if(closestFlash) closestFlash.remove()
//         })
//     })
// }
// closeFlashMessages()
// for showing errors might implement

 

// for showing errors might implement

function clearValidationErrors(form) {
  const errorEls = form.querySelectorAll('.error');
  errorEls.forEach(el => {
    el.textContent = '';
    el.style.display = 'none';
  });
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