import { handleSpecificError } from "./helpers";

function updateUserValidation(){
  const input = document.querySelector('#username');
  const charCount = document.querySelector('#charCount');
  const errorMessage = document.querySelector('.error-username');
  const submitBtn = document.querySelector('.user .update');

  const maxLength = 90;
  const usernameRegex = /^[a-zA-Z0-9._-]+$/;

  if (!input || !charCount) return;

  function remainingCharacters() {
    const value = input.value;
    const remaining = maxLength - value.length;
    charCount.textContent = `${Math.max(remaining, 0)} Remaining`;

    if (remaining < 0) {
      handleSpecificError('Submission blocked: input exceeds 90 characters.', 'username');
      submitBtn.disabled = true;
      return;
    }

    if (value && !usernameRegex.test(value)) {
      handleSpecificError('letters, numbers, dots, underscores, and hyphens.', 'username');
      submitBtn.disabled = true;
      return;
    }

    submitBtn.disabled = false;
  }
  input.addEventListener('input', remainingCharacters);
  remainingCharacters();
}


export {
  updateUserValidation,
};