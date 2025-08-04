@props(['name'])
<div class="error-message" id="error-{{ $name }}">
  @error($name) {{ $message }} @enderror
</div>