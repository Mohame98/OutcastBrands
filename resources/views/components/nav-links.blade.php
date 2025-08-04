@props(['active' => false])
@php
  $open = 'open';
  $closed = !$open;
@endphp
<a 
  class="link {{ $active ? $open : $closed }} "
  aria-current="{{ $active ? 'page' : 'false' }}"
  {{ $attributes }}
  >
  {{ $slot }}
</a>