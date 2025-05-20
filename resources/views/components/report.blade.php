<div class="modal-wrapper">
  <button 
    class="btn add-brand-btn modal-btn"
    aria-haspopup="add a brand form" 
    aria-controls="add-brand-modal" 
    aria-expanded="false">
    <i class="fa-solid fa-plus"></i>
    Add a Brand
  </button>
  <dialog id="add-brand-modal" class="add-brand-modal">
    <form action="" method="POST" class="action-form" data-action="report" enctype="multipart/form-data">
      @csrf
      <input type="hidden" name="reportable_type" value="{{ get_class($reportable) }}">
      <input type="hidden" name="reportable_id" value="{{ $reportable->id }}">
      {{-- step 1 --}}
      <fieldset>
        <header>
          <legend><h1>Select Report Reason</h1></legend>
        </header>
        <p>Choose up to 3 categories for your brand.</p>
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
            <label>
              <input type="radio" name="reason" value="{{ $value }}" required>
              {{ $value }}
            </label><br>
          @endforeach
        </div>

        <div class="btn-container">
          {{-- <button class="btn cancel" type="button" id="prevBtn2">Back</button> --}}
          <button class="btn update" type="submit" id="nextBtn1" data-step="1">Next</button>
        </div>
      </fieldset>
      <fieldset class="active">
        <header>
          <legend><h1>Describe the reason</h1></legend>
          <i class="fa-solid fa-xmark close-modal"></i>
        </header>
        <p>Try to further describe your report</p>

        <div class="form-step">
          <label for="description">Additional Details (optional):</label><br>
          <textarea name="description" rows="3" class="form-control"></textarea>
        </div>

        <div class="btn-container">
          <button class="btn update final" type="submit" data-step="2">Submit</button>
        </div>
      </fieldset>
    </form> 
  </dialog>
</div>