<header class="kb-header">
  <div class="container py-2">
    <div class="d-flex align-items-center justify-content-between">

      <div class="d-flex align-items-center gap-3">
        <a class="kb-brand" href="{{ route('home') }}">
          <img src="{{ asset('assets/brand/logo.avif') }}" alt="Khanabadosh logo">
        </a>
      </div>

      <nav class="kb-nav" aria-label="Main" id="kb-mobile-nav">
        <div class="kb-nav-mobile-head">
          <span class="kb-nav-mobile-title">Menu</span>
          <button class="kb-nav-close" type="button" aria-label="Close menu" data-nav-close>&times;</button>
        </div>
        <ul class="kb-menu">
          <li><a class="kb-nav-link" href="{{ route('home') }}">Home</a></li>
          <li class="kb-dropdown">
            <a class="kb-nav-link" href="{{ route('collections.show', ['slug' => 'winter25']) }}">
              <span class="kb-tag kb-tag-trending">Trending</span>
              <span>Winter '25</span>
              <i class="bi bi-chevron-down kb-nav-caret" aria-hidden="true"></i>
            </a>
            <div class="kb-dropdown-menu" aria-label="Winter '25">
              <ul class="kb-dropdown-list">
                <li class="has-submenu">
                  <a href="{{ route('collections.show', ['slug' => 'men-winter']) }}">
                    <span>Men</span>
                    <span class="arrow">&gt;</span>
                  </a>
                  <ul class="kb-submenu">
                    <li><a href="{{ route('collections.show', ['slug' => 'dewan-e-khaas']) }}">Dewan-e-Khaas Collection</a></li>
                    <li><a href="{{ route('collections.show', ['slug' => 'oxford']) }}">Oxford Collection</a></li>
                    <li><a href="{{ route('collections.show', ['slug' => 'jasper']) }}">Jasper Collection</a></li>
                    <li><a href="{{ route('collections.show', ['slug' => 'venus']) }}">Venus Collection</a></li>
                    <li><a href="{{ route('collections.show', ['slug' => 'jupiter']) }}">Jupiter Collection</a></li>
                    <li><a href="{{ route('collections.show', ['slug' => 'coral']) }}">Coral Collection</a></li>
                    <li><a href="{{ route('collections.show', ['slug' => 'peridot']) }}">Peridot Collection</a></li>
                  </ul>
                </li>
                <li><a class="kb-plain-link" href="{{ route('collections.show', ['slug' => 'women-all']) }}">Women</a></li>
              </ul>
            </div>
          </li>
          <li class="kb-dropdown">
            <a class="kb-nav-link" href="{{ route('collections.show', ['slug' => '12-12-sale']) }}">
              <span class="kb-tag kb-tag-sale">Sale</span>
              <span>12.12 Sale</span>
              <i class="bi bi-chevron-down kb-nav-caret" aria-hidden="true"></i>
            </a>
            <div class="kb-dropdown-menu kb-dropdown-menu--compact" aria-label="12.12 Sale">
              <ul class="kb-dropdown-list">
                <li><a class="kb-plain-link" href="{{ route('collections.show', ['slug' => '12-12-sale-men']) }}">Men</a></li>
                <li><a class="kb-plain-link" href="{{ route('collections.show', ['slug' => '12-12-sale-women']) }}">Women</a></li>
              </ul>
            </div>
          </li>
          <li class="kb-dropdown">
            <a class="kb-nav-link" href="{{ route('collections.show', ['slug' => 'men-all']) }}">
              <span>Men Unstitched '25</span>
              <i class="bi bi-chevron-down kb-nav-caret" aria-hidden="true"></i>
            </a>
            <div class="kb-dropdown-menu" aria-label="Men Unstitched '25">
              <ul class="kb-dropdown-list">
                <li class="has-submenu">
                  <a href="{{ route('collections.show', ['slug' => 'men-winter']) }}">
                    <span>Winter '25</span>
                    <span class="arrow">&gt;</span>
                  </a>
                  <ul class="kb-submenu">
                    <li><a href="{{ route('collections.show', ['slug' => 'dewan-e-khaas']) }}">Dewan-e-Khaas Collection</a></li>
                    <li><a href="{{ route('collections.show', ['slug' => 'oxford']) }}">Oxford Collection</a></li>
                    <li><a href="{{ route('collections.show', ['slug' => 'jasper']) }}">Jasper Collection</a></li>
                    <li><a href="{{ route('collections.show', ['slug' => 'venus']) }}">Venus Collection</a></li>
                    <li><a href="{{ route('collections.show', ['slug' => 'jupiter']) }}">Jupiter Collection</a></li>
                    <li><a href="{{ route('collections.show', ['slug' => 'coral']) }}">Coral Collection</a></li>
                    <li><a href="{{ route('collections.show', ['slug' => 'peridot']) }}">Peridot Collection</a></li>
                  </ul>
                </li>
                <li class="has-submenu">
                  <a href="{{ route('collections.show', ['slug' => 'all-season-men']) }}">
                    <span>All Seasons</span>
                    <span class="arrow">&gt;</span>
                  </a>
                  <ul class="kb-submenu">
                    <li><a href="{{ route('collections.show', ['slug' => 'sang-e-marmar']) }}">Sang e marmar Collection</a></li>
                    <li><a href="{{ route('collections.show', ['slug' => 'naltar']) }}">Naltar Collection</a></li>
                    <li><a href="{{ route('collections.show', ['slug' => 'deosai']) }}">Deosai Collection</a></li>
                  </ul>
                </li>
              </ul>
            </div>
          </li>
          <li class="kb-dropdown">
            <a class="kb-nav-link" href="{{ route('collections.show', ['slug' => 'women-all']) }}">
              <span>Women Unstitched '25</span>
              <i class="bi bi-chevron-down kb-nav-caret" aria-hidden="true"></i>
            </a>
            <div class="kb-dropdown-menu kb-dropdown-menu--compact" aria-label="Women Unstitched '25">
              <ul class="kb-dropdown-list">
                <li><a class="kb-plain-link" href="{{ route('collections.show', ['slug' => 'women-all']) }}">Winter'25</a></li>
              </ul>
            </div>
          </li>
          <li class="kb-dropdown">
            <a class="kb-nav-link" href="{{ route('policy') }}">
              <span>Policies</span>
              <i class="bi bi-chevron-down kb-nav-caret" aria-hidden="true"></i>
            </a>
            <div class="kb-dropdown-menu kb-dropdown-menu--policies" aria-label="Policies">
              <ul class="kb-dropdown-list">
                <li>
                  <a class="kb-policy-link" href="{{ route('policy', ['title' => 'Shipping Policy']) }}">
                    <i class="bi bi-box-seam kb-policy-icon kb-icon-shipping"></i>
                    <span>Shipping Policy</span>
                  </a>
                </li>
                <li>
                  <a class="kb-policy-link" href="{{ route('policy', ['title' => 'Exchange & Return Policy']) }}">
                    <i class="bi bi-arrow-repeat kb-policy-icon kb-icon-exchange"></i>
                    <span>Exchange & Return Policy</span>
                  </a>
                </li>
                <li>
                  <a class="kb-policy-link" href="{{ route('policy', ['title' => 'FAQs']) }}">
                    <i class="bi bi-question-circle kb-policy-icon kb-icon-faq"></i>
                    <span>FAQs</span>
                  </a>
                </li>
                <li>
                  <a class="kb-policy-link" href="{{ route('policy', ['title' => 'Terms & Conditions']) }}">
                    <i class="bi bi-file-text kb-policy-icon kb-icon-terms"></i>
                    <span>Terms & Conditions</span>
                  </a>
                </li>
                <li>
                  <a class="kb-policy-link" href="{{ route('policy', ['title' => 'Privacy Policy']) }}">
                    <i class="bi bi-shield-lock kb-policy-icon kb-icon-privacy"></i>
                    <span>Privacy Policy</span>
                  </a>
                </li>
              </ul>
            </div>
          </li>
          <li><a class="kb-nav-link" href="{{ route('lookbook') }}">Lookbook</a></li>
        </ul>
      </nav>

      <div class="kb-header-icons d-flex align-items-center">
        <button class="kb-burger" type="button" aria-label="Open menu" aria-controls="kb-mobile-nav" aria-expanded="false" data-nav-toggle>
          <span class="kb-burger-bars" aria-hidden="true"></span>
        </button>
        <button class="btn" type="button" aria-label="Search" data-search-open><i class="bi bi-search"></i></button>
        <div class="position-relative">
          <a class="btn" href="{{ route('wishlist') }}" aria-label="Wishlist"><i class="bi bi-heart"></i></a>
          <span class="kb-icon-badge" data-wishlist-count>0</span>
        </div>
        <div class="position-relative">
          <a class="btn" href="{{ route('cart') }}" aria-label="Cart"><i class="bi bi-bag"></i></a>
          <span class="kb-icon-badge" data-cart-count>0</span>
        </div>
      </div>

    </div>
  </div>
</header>

<div class="kb-nav-overlay" data-nav-overlay data-nav-close></div>
