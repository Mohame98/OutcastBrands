import { 
  handleModals
} from './modals.js';

import { 
  handleMultipleMediaUpload,
  handleMedia,
} from './mediaInputs.js';

import { 
  updateUserValidation 
} from './validation.js';

import {
  main
} from './formResponses/handleFormSubmissions.js'

import {
  createNode,
  setupPasswordToggles,
  handleSpecificError,
  selectOnlyThreeCategories,
  trimAllFields,
  closeModal,
  closeDetails,
} from './helpers.js';

export function init() {
  handleModals();
  setupPasswordToggles();
  handleMedia();
  createNode();
  handleSpecificError();
  updateUserValidation();
  handleMultipleMediaUpload();
  selectOnlyThreeCategories();
  closeDetails();
  trimAllFields();
  main();
}