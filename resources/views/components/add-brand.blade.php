<div class="modal-wrapper add-brand">
  <button 
    class="btn add-brand-btn modal-btn"
    aria-haspopup="add a brand form" 
    aria-controls="add-brand-modal" 
    title="Add a Brand"
    aria-expanded="false">
      <i class="fa-solid fa-plus"></i>
      <span class="btn-text">
        <span class="submit">Submit</span>
        <span>Brand</span>
      </span>  
  </button>
  <dialog id="add-brand-modal" class="add-brand-modal" data-action-url="{{ route('brands.store.step1') }}">
    <form action="" method="POST" class="action-form" data-action="add-brand" enctype="multipart/form-data" data-form-base="/add-brands" data-total-steps="4">
      @csrf
      <fieldset class="multi-field active" id="fieldset1" data-action-url="{{ route('brands.store.step1') }}">
        <header class="modal-headers">
          <legend><h1>1 - Brand Info</h1></legend>
          @include('components.close-modal')
        </header>
        <p>Enter brand details below.</p>

        <div class="form-group">
          <label for="title">
            <span>Brand Title *</span>
            <input autofocus type="text" id="title" name="title"
              required
              maxlength="100"
            >
          </label>
          <x-form-error name="title" />
        </div>

        <div class="form-group">
          <label for="sub_title" class="sub_title">
            <span>Sub Title *</span>
            <input type="text" id="sub_title" name="sub_title"
              required
              maxlength="200"
            >
          </label>
          <x-form-error name="sub_title" />
        </div>

        <div class="form-group">
          <label for="website">
            <span>Website Link (optional)</span>
            <input type="url" id="website" name="website"
              maxlength="255"
            >
          </label>
          <x-form-error name="website" />
        </div>

        <div class="row">
          <div class="form-group">
            <label for="location">
              <span>Location *</span>
              <input type="text" id="location" name="location"
                required
                maxlength="60"
              >
            </label>
            <x-form-error name="location" />
          </div>
          
          <div class="form-group">
            <label for="launch_date">
              <span>Launch Date (optional)</span>
              <input type="date" id="launch_date" name="launch_date">
            </label>
            <x-form-error name="launch_date" />
          </div>
        </div>

        <div class="btn-container">
          <button class="btn update" type="submit" id="nextBtn1" data-step="1" data-action="next-step">Next</button>
        </div>
      </fieldset>

      <fieldset class="multi-field description" id="fieldset2">
        <header class="modal-headers">
          <legend><h1>2 - Add Description</h1></legend>
          @include('components.close-modal')
        </header>
        <p>Enter a description for your brand below.</p>

        <div class="form-group">
          <div id="editor-container"></div>
          <input type="hidden" name="description" id="quill-content-description"
            maxlength="5000"  
          >
          <x-form-error name="description" />
        </div>

        <button class="btn quill-clear-btn" id="quillDeleteButton">Clear Text</button>

        <div class="btn-container">
          <button type="button" class="btn cancel" data-back-button>Back</button>
          <button class="btn update" type="submit" id="nextBtn1" data-step="2" data-action="next-step">Next</button>
        </div>
      </fieldset>

      <fieldset class="multi-field brand-image-field" id="fieldset3">
        <header class="modal-headers">
          <legend><h1>3 - Upload Images</h1></legend>
          @include('components.close-modal')
        </header>
        <p>Submit at most 4 images of your brand/product.</p>
        <p class="featured-noti">The first image will be the featured image.</p>

        <div class="multiple-photos">
          <label for="brand-image">
            <div class="media-input brand">
              <label for="brand-image" class="media-label" tabindex="0">
                <span>Drag or upload</span> <i class="fa-solid fa-cloud-arrow-up"></i>
              </label>
              <input type="file" accept="image/*" data-selector="multiple-photos" data-multiple="true" data-maxFiles="4" name="photos[]" multiple id="brand-image" aria-label="Drag and Drop or upload media" data-maxSize="4194304"
              accept=".png, .jpg, .jpeg">
              <div class="media-preview brand slider"></div>
              <div class="upload-info">
                <p>Formats: JPG, PNG</p>
                <P>Max Total Size: 4MB</P>
              </div>
              <button class="clear-media-btn brand" style="display: none;">
                <span>
                  <i class="fa-solid fa-trash-can"></i>
                  <span class="hover-caption">Remove Media</span>
                </span>
              </button>
            </div>
          </label>
        </div>

        <div class="form-group form-error-wrapper">
          <x-form-error name="photos" />
        </div>

        <p class="number-files"><span class="files-digit">0</span>/4</p>
        
        <div class="btn-container">
          <button type="button" class="btn cancel" data-back-button>Back</button>
          <button class="btn update" type="submit" id="nextBtn2" data-step="3" data-action="next-step">Next</button>
        </div>
      </fieldset>

      <fieldset class="multi-field" id="fieldset4">
        <header class="modal-headers">
          <legend><h1>4 - Select 3 Categories</h1></legend>
          @include('components.close-modal')
        </header>
        <p>Choose up to 3 categories for your brand.</p>
        @php
        $categories = [
          'Fashion', 'Beauty', 'Home', 'Technology', 'Sports',
          'Automotive', 'Food', 'Entertainment', 'Business', 'Software', 
          'Media', 'Services', 'Luxury'
        ];
        @endphp
        <div class="category-list multiple-categories" data-limit="3">
          @foreach ($categories as $category)
          <div class="form-group">
            <label class="category-button" for="checkbox-{{ $category }}">
              <input
                type="checkbox"
                name="categories[]"
                value="{{ $category }}"
                class="category-checkbox"
                id="checkbox-{{ $category }}"
                data-selector="multiple-categories"
              >
              <span>{{ $category }}</span>
            </label>
          </div>
          @endforeach
        </div>

        <div class="btn-container">
          <button type="button" class="btn cancel" data-back-button>Back</button>
          <button class="btn update final" type="submit" data-step="4">Submit</button>
        </div>
      </fieldset>
    </form> 
  </dialog>
  </div>