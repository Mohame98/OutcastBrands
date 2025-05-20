import { handleSpecificError, createNode } from "./helpers";

function handleMedia(){
  const fileInputs = document.querySelectorAll('#id_media, #profile-image');
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
    const errorMessage = document.querySelector('.error-profile_image');
    const submitBtn = document.querySelector('.profile-image-modal .update')

    const allowedTypes = ['image/png', 'image/jpeg'];
    if (!allowedTypes.includes(file.type)) {
      handleSpecificError('Only PNG and JPG images are allowed.', 'profile_image');
      submitBtn.disabled = true;
      return;
    }

    if (file.size > 1048576){
      handleSpecificError('File is too large. Choose image under 1KB.', 'profile_image');
      submitBtn.disabled = true;
      return;
    } 

    errorMessage.style.display = 'none';
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

function handleMultipleMediaUpload() {
  const fileInputs = document.querySelectorAll('.media-input input[type="file"][data-multiple="true"]');
  fileInputs.forEach(fileInput => {
    const container = fileInput.closest('.media-input.brand');
    const preview = container.querySelector('.media-preview.brand');
    const clearBtn = container.querySelector('.clear-media-btn.brand');
    const maxFiles = parseInt(fileInput.dataset.maxFiles || "4");
    const maxTotalSize = parseInt(fileInput.dataset.maxSize || "5242880"); // 5MB

    let imageFiles = [];

    fileInput.addEventListener('change', e => {
      const newFiles = Array.from(e.target.files).filter(f => f.type.startsWith('image/'));
      const proposedFiles = [...imageFiles, ...newFiles];
      const totalSize = proposedFiles.reduce((sum, f) => sum + f.size, 0);
      const numOfFiles = proposedFiles.length;
      const numberContainer = document.querySelector('.files-digit');
      
      if (numOfFiles > maxFiles) {
        handleSpecificError(`You can only upload up to ${maxFiles} images.`, 'brand_image');
        return;
      }

      if (totalSize > maxTotalSize) {
        handleSpecificError(`File size cannot exceed 5mb.`, 'brand_image');
        return;
      }

      document.querySelector('.error-brand_image').textContent = '';
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

      const newFiles = Array.from(e.dataTransfer.files).filter(f => f.type.startsWith('image/'));
      const proposedFiles = [...imageFiles, ...newFiles];
      const totalSize = proposedFiles.reduce((sum, f) => sum + f.size, 0);
      const numberContainer = document.querySelector('.files-digit');
      const numOfFiles = proposedFiles.length;

      if (numOfFiles > maxFiles) {
        handleSpecificError(`You can only upload up to ${maxFiles} images.`, 'brand_image');
        return;
      }

      console.log(numOfFiles)
      if (numOfFiles === 0) {
        handleSpecificError(`The Image field is required`, 'brand_image');
        return;
      }

      if (totalSize > 5242880) {
        console.log(totalSize, maxTotalSize)
        handleSpecificError(`Total size cannot exceed 5mb.`, 'brand_image');
        return;
      }

      document.querySelector('.error-brand_image').textContent = '';
      numberContainer.textContent = numOfFiles
      imageFiles = proposedFiles;
      const dt = new DataTransfer();
      imageFiles.forEach(file => dt.items.add(file));
      fileInput.files = dt.files;
      updateMultiplePreview(imageFiles, preview, container, clearBtn);
    });

    if (clearBtn) {
      const numberContainer = document.querySelector('.files-digit');
      const errorMessage = document.querySelector('.error-brand_image');
      clearBtn.addEventListener('click', e => {
        e.preventDefault();
        imageFiles = [];
        handleSlickInit();
        fileInput.value = '';
        errorMessage.style.display = 'none';
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