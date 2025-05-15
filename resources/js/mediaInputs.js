export function handleMedia(){
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

    const allowedTypes = ['image/png', 'image/jpeg'];
    if (!allowedTypes.includes(file.type)) {
      handleError('Only PNG and JPG images are allowed.', 'profile_image');
      return;
    }

    if (file.size > 1048576){
      handleError('File is too large. Choose image under 1KB.', 'profile_image');
      return;
    } 
    
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

function handleError(message, field){
  const errorEl = document.querySelector(`.error-${field}`);
  if (errorEl) {
    errorEl.textContent = `${message}`;
    errorEl.style.display = 'block';
  }
}