import { 
  handleResponse, 
  handleGeneralErrors, 
  showFlashMessage, 
} from "./handleResponses";

async function handleFormSubmissions(event) {
  event.preventDefault();
  const form = event.target.closest('form'); 
  if (!form) return;
  const formData = new FormData(form);
  const formMethod = form.method;
  
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
  const actionUrl = multiStepFormAction(event, form);
  await sendForm(actionUrl, formMethod, form, formData, csrfToken);
}

async function sendForm(actionUrl, formMethod, form, formData, csrfToken) {
  const options = {
    method: formMethod,
    body: formData,
    headers: {
      'X-CSRF-TOKEN': csrfToken,
      'Accept': 'application/json',
    }
  };
  await handleRequest(actionUrl, options, form);
}
    
async function handleRequest(actionUrl, options, form) {
  try {
    const response = await fetch(actionUrl, options);
    const result = await response.json();
    if (!response.ok) {
      switch (response.status) {
        case 401:
          showFlashMessage({ message: true }, 'Please sign in to continue');
          break;
        case 422:
          handleGeneralErrors(result, form);
          break;
        case 403:
          window.location.href = '/email/verify';
          break;
        default:
          console.error('Unexpected error:', result);
      }
      return;
    }

    // called twice handles removing error messages if success
    handleGeneralErrors(result, form);
    handleResponse(result, form, actionUrl);
  } catch (error) {
    console.error('Network Error:', error);
  }
}

function main() {
  document.body.addEventListener('submit', function(event) {
    if (event.target && event.target.matches('.action-form')) {
      handleFormSubmissions(event);  
    }
  });
}

function multiStepFormAction(event, form) {
  const button = event.submitter || event.target.closest('button');
  const step = button?.dataset?.step;
  const baseUrl = form.dataset.formBase;

  let actionUrl;
  if (step) {
    actionUrl = `${baseUrl}/step-${step}`;
  } else {
    actionUrl = form.action;
  }
  return actionUrl;
}

export {
  main,
};
