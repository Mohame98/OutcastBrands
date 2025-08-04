import { handleSpecificError, clearSpecificError, createNode } from "./helpers";

function handleMedia(){
  const fileInputs = document.querySelectorAll('#id_media, #profile_image');
  fileInputs.forEach(fileInput => {
    const container = fileInput.closest('.media-input');
    const imageInfo = document.querySelector('.upload-info')
    const preview = container.querySelector('.media-preview');
    const clearBtn = container.querySelector('.clear-media-btn');
    fileInput.addEventListener('change', e => handleFileSelect(e, fileInput, preview, container, clearBtn));

    container.addEventListener('dragover', e => {
      e.preventDefault();
      container.classList.add('dragging');
    });

    container.addEventListener('dragleave', e => {
      e.preventDefault();
      container.classList.remove('dragging');
    });

    container.addEventListener('drop', e => {
      e.preventDefault();
      container.classList.remove('dragging');

      const file = e.dataTransfer.files[0];
      if (file) {
        const dt = new DataTransfer();
        dt.items.add(file);
        fileInput.files = dt.files;
        handleFileSelect(e, fileInput, preview, container, clearBtn);
      }
    });

    if (clearBtn) {
      clearBtn.addEventListener('click', e => {
        e.preventDefault();
        clearSpecificError("profile_image");
        preview.style.backgroundImage = '';
        container.classList.remove('has-image');
        fileInput.value = '';
        clearBtn.style.display = 'none';
      });
    }
  });

  function handleFileSelect(e, input, preview, container, clearBtn) {
    e.preventDefault();
    const file = e.target.files?.[0] || e.dataTransfer?.files[0];
    if (!file || !file.type.startsWith('image')) return;
    const submitBtn = document.querySelector('.profile-image-modal .update')

    const allowedTypes = ['image/png', 'image/jpeg'];
    if (!allowedTypes.includes(file.type)) {
      handleSpecificError('JPG and PNG images only.', 'profile_image');
      submitBtn.disabled = true;
      return;
    }

    if (file.size > 1048576){
      handleSpecificError('File is too large. Choose image under 1MB.', 'profile_image');
      submitBtn.disabled = true;
      return;
    } 

    clearSpecificError("profile_image");
    submitBtn.disabled = false;
    
    preview.innerHTML = '';
    const reader = new FileReader();
    reader.onload = e => {
      const url = e.target.result;
      preview.style.backgroundImage = `url('${url}')`;
      container.classList.add('has-image'); 
      if (clearBtn) clearBtn.style.display = 'block';
    };
    reader.readAsDataURL(file);
  }
}

// create a mb tracker aswell
function handleMultipleMediaUpload() {
  const allowedTypes = ['image/png', 'image/jpeg'];
  const fileInputs = document.querySelectorAll('.media-input input[type="file"][data-multiple="true"]');
  fileInputs.forEach(fileInput => {
    const container = fileInput.closest('.media-input.brand');
    const preview = container.querySelector('.media-preview.brand');
    const clearBtn = container.querySelector('.clear-media-btn.brand');
    const maxFiles = parseInt(fileInput.dataset.maxFiles || "4");
    const maxTotalSize = parseInt(fileInput.dataset.maxSize || "4194304");

    let imageFiles = [];

    fileInput.addEventListener('change', e => {
      const allFiles = Array.from(e.target.files);
      const newFiles = allFiles.filter(f => allowedTypes.includes(f.type));
      const proposedFiles = [...imageFiles, ...newFiles];
      const totalSize = proposedFiles.reduce((sum, f) => sum + f.size, 0);
      const numOfFiles = proposedFiles.length;
      const numberContainer = document.querySelector('.files-digit');

      console.log(numOfFiles)
      
      if (numOfFiles > maxFiles) {
        handleSpecificError(`You can only upload up to 4 images.`, 'photos');
        return;
      }

      if (totalSize > maxTotalSize) {
        handleSpecificError(`Total size cannot exceed 4mb.`, 'photos');
        return;
      }

      if (newFiles.length !== allFiles.length) {
        handleSpecificError('Only JPG and PNG images are allowed.', 'photos');
        return;
      }

      clearSpecificError("photos");
      numberContainer.textContent = numOfFiles

      imageFiles = proposedFiles;
      const dt = new DataTransfer();
      imageFiles.forEach(file => dt.items.add(file));
      fileInput.files = dt.files;
      updateMultiplePreview(imageFiles, preview, container, clearBtn);
    });

    container.addEventListener('dragover', e => {
      e.preventDefault();
      container.classList.add('dragging');
    });

    container.addEventListener('dragleave', e => {
      e.preventDefault();
      container.classList.remove('dragging');
    });

    container.addEventListener('drop', e => {
      e.preventDefault();
      container.classList.remove('dragging');

      const allFiles = Array.from(e.dataTransfer.files);
      const newFiles = allFiles.filter(f => allowedTypes.includes(f.type));
      const proposedFiles = [...imageFiles, ...newFiles];
      const totalSize = proposedFiles.reduce((sum, f) => sum + f.size, 0);
      const numberContainer = document.querySelector('.files-digit');
      const numOfFiles = proposedFiles.length;

      if (numOfFiles > maxFiles) {
        handleSpecificError(`You can only upload up to 4 images.`, 'photos');
        return;
      }

      if (totalSize > maxTotalSize) {
        handleSpecificError(`Total size cannot exceed 4mb.`, 'photos');
        return;
      }

      if (newFiles.length !== allFiles.length) {
        handleSpecificError('Only JPG and PNG images are allowed.', 'photos');
        return;
      }

      clearSpecificError("photos");
      numberContainer.textContent = numOfFiles
      imageFiles = proposedFiles;
      const dt = new DataTransfer();
      imageFiles.forEach(file => dt.items.add(file));
      fileInput.files = dt.files;
      updateMultiplePreview(imageFiles, preview, container, clearBtn);
    });

    if (clearBtn) {
      const numberContainer = document.querySelector('.files-digit');
      clearBtn.addEventListener('click', e => {
        e.preventDefault();
        imageFiles = [];
        handleSlickInit();
        fileInput.value = '';
        clearSpecificError("photos");
        numberContainer.textContent = 0;
        container.classList.remove('has-image');
        clearBtn.style.display = 'none';
      });
    }

    function updateMultiplePreview(images, preview, container, clearBtn) {
      handleSlickInit();
      images.forEach(file => {
        const reader = new FileReader();
        reader.onload = e => {
          const slideContainer = createNode('div', null, null, 'slide custom-slide');
          const url = e.target.result;
          const img = createNode('div', null, slideContainer, 'brand-image');
          img.style.backgroundImage = `url('${url}')`;
          $('.media-preview.brand').slick('slickAdd', slideContainer.outerHTML);
        };
        reader.readAsDataURL(file);
      });
      container.classList.add('has-image');
      if (clearBtn) clearBtn.style.display = 'block';
    }
  });
}

function handleSlickInit(){
  const $slider = $('.media-preview.brand');
  const indexesToRemove = [];
  $slider.find('.custom-slide').each(function () {
    const slickIndex = $(this).data('slick-index');
    if (typeof slickIndex !== 'undefined') {
      indexesToRemove.push(slickIndex);
    }
  });

  indexesToRemove.sort((a, b) => b - a);
  indexesToRemove.forEach(index => {
    $slider.slick('slickRemove', index);
  });
}

export {
  handleMedia,
  handleMultipleMediaUpload,
};