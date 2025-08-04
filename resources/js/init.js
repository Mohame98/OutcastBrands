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
  handleAllPopovers
} from './popovers.js'

import {
  initBrandImgSlider,
  initBrandMediaPreview,
} from './sliders.js'

import {
  createNode,
  setupPasswordToggles,
  handleSpecificError,
  selectOnlyThreeCategories,
  // trimAllFields,
  closeModal,
  backButtons,
  closeDetails,
} from './helpers.js';

export function init() {
  backButtons();
  handleModals();
  setupPasswordToggles();
  handleMedia();
  createNode();
  handleSpecificError();
  updateUserValidation();
  handleMultipleMediaUpload();
  selectOnlyThreeCategories();
  handleAllPopovers(),
  initBrandImgSlider(),
  initBrandMediaPreview(),
  closeDetails();
  // trimAllFields();
  main();
}