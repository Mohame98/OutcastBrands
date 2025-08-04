<div class="context-menu-container">
  <button class="btn context-menu-btn" id="context-menu-btn" popovertarget="context-menu" popovertargetaction="toggle" aria-haspopup="menu" title="Menu" aria-expanded="false" aria-controls="context-menu">
    <i class="fa-solid fa-ellipsis"></i>
  </button>
  <nav class="context-menu popover" id="context-menu" aria-label="context menu" popover>
    <ul>
      <x-interactions.save :brand="$brand"/>
      <x-interactions.report :model="$brand" type="brand" />
    </ul>
  </nav>
</div>