<details class="context-menu-container">
  <summary>
    <i class="fa-solid fa-ellipsis"></i>
  </summary>
  <div class="context-menu">
    <div class="save context-item">
      <form method="POST" action="{{ route('brands.save', $brand) }}" class="action-form" data-action="save">
        @csrf
        <button class="btn save-btn" type="submit" aria-label="save brand {{ $brand->title }}" data-close-details>
          {!! $brand->savers->contains(auth()->id()) ? '<i class="fa-solid fa-bookmark"></i> Saved' : '<i class="fa-regular fa-bookmark"></i> Unsaved' !!}
        </button>
      </form>
    </div>
    <div class="report context-item">
      <div class="modal-wrapper">
        <button 
          class="btn report-btn modal-btn"
          aria-haspopup="Report {{ $type }} {{ $brand->title }}" 
          aria-controls="report-{{ $type }}" 
          aria-expanded="false">
          <i class="fa-solid fa-flag"></i>
          Report
        </button>
        <dialog id="report-{{ $type }}" class="report report-{{ $type }}">
          <form action="" method="POST" class="action-form" data-action="report" enctype="multipart/form-data" data-form-base="/report-{{ $type }}" data-total-steps="2">
            @csrf
            <input type="hidden" name="reportable_type" value="brand">
            <input type="hidden" name="reportable_id" value="{{ $brand->id }}">
    
            {{-- step 1 --}}
            <fieldset class="multi-field active">
              <header>
                <legend><h1>Select a report reason</h1></legend>
                @include('components.close-modal')
              </header>

              <p>Use the form below to report content that violates our guidelines.</p>
              
              @php
              $reasons = [
                'Sexual content',
                'Violent or repulsive content',
                'Hateful or abusive content',
                'Harassment or bullying',
                'Misinformation',
                'Child abuse',
                'Promotes terrorism',
                'Spam or misleading',
                'Legal issue',
                'Captions issue',
              ];
              @endphp
              <div class="report-list">
                @foreach($reasons as $value)
                <div class="form-group">
                  <label for="reason_{{ $value }}" class="reason">
                    <input type="radio" name="reason" value="{{ $value }}" id="reason_{{ $value }}">
                    {{ $value }}
                  </label>
                  <x-form-error name="reason" />
                </div>
                @endforeach
              </div>

              <div class="btn-container">
                <button class="btn update" type="submit" id="nextBtn1" data-step="1">Next</button>
              </div>

            </fieldset>
            {{-- step 2 --}}
            <fieldset class="multi-field">
              <header>
                <legend><h1>Describe the reason</h1></legend>
                @include('components.close-modal')
              </header>
              <p>Try to further describe your report (optional)</p>

              <div class="form-group">
                <label for="report_description" class="report_description">
                  <span>Additional Details</span>
                  <textarea name="report_description" id="report_description" rows="3" class="form-control"></textarea>
                </label>
                <x-form-error name="report_description" />
              </div>

              <div class="btn-container">
                <button class="btn update final" type="submit" data-step="2">Submit</button>
              </div>
            </fieldset>
          </form> 
        </dialog>
      </div>
    </div>
  </div>
</details>