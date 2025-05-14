@props(['name'])
<p class="error error-{{ $name }}">
  @error($name) {{ $message }} @enderror
</p>