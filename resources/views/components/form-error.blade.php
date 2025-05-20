@props(['name'])
<span class="error-message error-{{ $name }}">
  @error($name) {{ $message }} @enderror
</span>