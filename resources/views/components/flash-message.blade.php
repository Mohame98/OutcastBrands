@if(session('flash_message'))
<section class="flash-message" data-action="flash-message" role="alert" aria-live="assertive">
  <div>
    <i class="fa-solid fa-info"></i>
    <p class="flash-text">{{ session('flash_message') }}</p>
  </div>
  <i class="fa-solid fa-xmark"></i>
</section>
@endif


