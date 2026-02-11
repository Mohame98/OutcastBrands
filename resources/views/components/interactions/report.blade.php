<li class="report context-item">
  <div class="modal-wrapper">
    <button 
      class="btn report-btn modal-btn item-btn"
      aria-haspopup="Report {{ $type }}" 
      title="Report {{ $type }}"
      aria-controls="report-{{ $type }}-{{ $model->id }}"  
      aria-expanded="false" type="submit">
        <i class="fa-solid fa-flag"></i>
        <span class="btn-text">Report</span>
    </button>
    <dialog id="report-{{ $type }}-{{ $model->id }}" class="report-modal report-{{ $type }}">
      <form action="" method="POST" class="action-form" data-action="report" enctype="multipart/form-data" data-form-base="/report" data-total-steps="2">
        @csrf
        <input type="hidden" name="reportable_type" value="{{ $type }}">
        <input type="hidden" name="reportable_id" value="{{ $model->id }}">

        {{-- step 1 --}}
        <fieldset class="multi-field active">
          <header class="modal-headers">
            <legend><h1>Report reason</h1></legend>
            @include('components.close-modal')
          </header>

          <p>Use the form below to report content that violates our guidelines.</p>
          
          @php
          $reasons = [
            'Violent or repulsive content',
            'Sexual content',
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
              <label for="reason_{{ $value }}_{{ $model->id }}" class="reason" title="{{ $value }}">
                <input type="radio" name="reason" value="{{ $value }}" id="reason_{{ $value }}_{{ $model->id }}" class="radio-btn">
                {{ $value }}
                <x-form-error name="reason" />
              </label>
            </div>
            @endforeach
          </div>

          <div class="btn-container">
            <button class="btn update" type="submit" id="nextBtn1" data-step="1" data-action="next-step">Next</button>
          </div>

        </fieldset>
        {{-- step 2 --}}
        <fieldset class="multi-field">
          <header class="modal-headers">
            <legend><h1>Describe the reason</h1></legend>
            @include('components.close-modal')
          </header>
          <p>Try to further describe your report (optional)</p>

          <div class="form-group">
            <label for="report_description" class="report_description">
              <span>Additional Details</span>
              <textarea name="report_description" id="report_description" rows="3" class="form-control" 
                maxlength="500"
                title="Provide additional details about your report"
              ></textarea>
            </label>
            <x-form-error name="report_description" />
          </div>

          <div class="btn-container">
            <button type="button" class="btn cancel" data-back-button>Back</button>
            <button class="btn update final" type="submit" data-step="2">Submit</button>
          </div>
        </fieldset>
      </form> 
    </dialog>
  </div>
</li>