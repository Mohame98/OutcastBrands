<x-main-layout>
  <section class="user-profile" data-user-id="{{ $user->id }}" id="user-profile">
    <div class="container">
      <div class="user-profile-content">
        <div class="user-profile-container">
          <div class="avatar-container">
            @if($user->profile_image)
            <div 
              class="avatar img" 
              style="background-image: url('{{ asset('storage/' . $user->profile_image) }}');"
            ></div>
            @else
            <div class="avatar letter">
              {{ strtoupper(substr($user->email, 0, 1)) }}
            </div>
            @endif
          </div>
          <div class="user-info">
            <div class="user">
              <div class="info-header">
                <h3>{{ $user->username }}</h3>
              </div>
              @auth
              <div class="context-menu-container">
                <button 
                  class="btn context-menu-btn" 
                  id="context-menu-btn" 
                  popovertarget="user-menu-{{ $user->id }}" 
                  popovertargetaction="toggle" 
                  aria-haspopup="menu" 
                  title="Menu" 
                  aria-expanded="false" 
                  aria-controls="user-menu-{{ $user->id }}"
                >
                  <i class="fa-solid fa-ellipsis-vertical"></i>
                </button>
                <nav class="context-menu popover" id="user-menu-{{ $user->id }}" aria-label="context menu" popover>
                  <ul>
                    {{-- <li class="context-item">Copy Url</li> --}}
                    <x-interactions.report type="user" :model="$user" />
                  </ul>
                </nav>
              </div>
              @endauth
            </div>
            
            <p class="user-bio">{{ $user->bio }}</p>
            @if ($user->user_location)
            <div class="location">
              <i class="fa-solid fa-location-dot"></i>
              {{ Str::limit($user->user_location, 30, '...') }}
            </div>
            @endif
            
            <div class="btn-container">
              @if ($user->instagram)
              <a href="{{ $user->instagram }}" target="_blank" rel="noopener noreferrer">Instagram</a>
              @endif
              
              @if(Auth::check() && Auth::id() !== $user->id)
              <div class="modal-wrapper">
                <button 
                  class="btn get-in-touch modal-btn white-btn"
                  aria-haspopup="show get in touch form" 
                  aria-controls="get-in-touch-modal" 
                  aria-expanded="false">
                  Get in Touch
                </button>
                <dialog id="get-in-touch-modal" class="get-in-touch-modal">
                  <form 
                    action="{{ route('contact.send', $receiver->id) }}" 
                    method="POST"
                    class="action-form"
                    data-action="send-contact-message"
                  >
                    <fieldset class="get-in-touch-field" id="get-in-touch-field">
                      <header class="modal-headers">
                        <legend>
                          <div class="avatar-container">
                            @if($user->profile_image)
                            <div 
                              class="avatar img" 
                              style="background-image: url('{{ asset('storage/' . $user->profile_image) }}');">
                            </div>
                            @else
                            <div class="avatar letter">
                              {{ strtoupper(substr($user->email, 0, 1)) }}
                            </div>
                            @endif
                          </div>
                          <h1>Get in touch</h1>
                        </legend>
                        @include('components.close-modal')
                      </header>
                      <p>Send a message to <span class="color-blue">{{ $user->username }}</span> and receive a reply through email</p>

                      <ul>
                        <li>
                          <i class="fa-solid fa-check"></i>
                          Follow message guidelines
                        </li>
                        <li>
                          <i class="fa-solid fa-check"></i>
                          No spam
                        </li>
                        <li>
                          <i class="fa-solid fa-check"></i>
                          No Hate
                        </li>
                      </ul>
                      @csrf
                      <div class="form-group">
                        <label for="subject" class="subject">
                          <span>Subject (Optional)</span>
                          <input type="text" id="subject" name="subject">
                        </label>
                        <x-form-error name="subject" />
                      </div>

                      <div class="form-group">
                        <label for="message">
                          <span>Message *</span>
                          <textarea name="message" id="message" rows="5" required></textarea>
                        </label>
                        <x-form-error name="message" />
                      </div>

                      <div class="btn-container">
                        <button class="btn update" type="submit">Send Message</button>
                      </div>
                    </fieldset>
                  </form>
                </dialog>
              </div>
              @endif
              @if ($isOwner)
                <a class="white-btn edit-profile-btn" href="{{ route('account.profile') }}">Edit Profile</a>
              @endif
          </div>
        </div>
        </div>
      </div>

      <div class="filter-container">
        <div class="filter-section">
          <div class="filter-btns">
            <button name="filter" value="all" data-filter="filter:all"
              class="filter-btn active">
              All Brands
            </button>

            <button name="filter" value="voted" data-filter="filter:voted"
              class="filter-btn">
              Voted Brands
            </button>
          </div>
        </div>

        <div class="search-input">
          <div id="search" class="search-container">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input name="search" type="text" id="search-input" placeholder="Search" />
          </div>

          <select name="sort" id="sort-by">
            <option value="featured">Default</option>
            <option value="newest">Newest</option>
            <option value="oldest">Oldest</option>
            <option value="most popular">Most Popular</option>
          </select>
        </div>
        
        <div class="active-filters">
          <div id="active-filters">

          </div>
        </div>

        <section class="popular-brands-top-layer">
          <div class="grid" id="brands-container"></div>
          <button type="button" class="load-more btn white-btn">Load more</button>
        </section>
      </div>
    </div>
  </section>
  @include('layouts.newsletter')
</x-main-layout>