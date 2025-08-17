import { 
  handleModals,
  toggleLoginModals,
} from './modals.js';

import { 
  runQuillEditor,
} from './quillTextEditor.js';

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
  initFilterSystem,
} from './filterSystem/mainFilterSys.js'

import {
  createNode,
  setupPasswordToggles,
  handleSpecificError,
  selectOnlyThreeCategories,
  // trimAllFields,
  closeModal,
  backButtons,
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
  runQuillEditor();
  initFilterSystem();
  // closeDetails();
  toggleLoginModals();
  // trimAllFields();
  main();
}