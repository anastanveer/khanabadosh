<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>{{ $metaTitle ?? (isset($pageTitle) ? 'Khanabadosh - ' . $pageTitle : 'Khanabadosh') }}</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="{{ asset('css/khanabadosh.css') }}" rel="stylesheet">
</head>
<body>
  @include('partials.header')

  <div class="kb-mobile-tab" aria-label="Quick actions">
    <button class="kb-mobile-tab-item" type="button" aria-label="Search" data-search-open>
      <i class="bi bi-search"></i>
      <span class="kb-tab-label">Search</span>
    </button>
    <a class="kb-mobile-tab-item" href="{{ route('wishlist') }}" aria-label="Wishlist">
      <i class="bi bi-heart"></i>
      <span class="kb-tab-label">Wishlist</span>
      <span class="kb-tab-badge" data-wishlist-count>0</span>
    </a>
    <a class="kb-mobile-tab-item" href="{{ route('cart') }}" aria-label="Cart">
      <i class="bi bi-bag"></i>
      <span class="kb-tab-label">Cart</span>
      <span class="kb-tab-badge" data-cart-count>0</span>
    </a>
  </div>

  <div class="kb-search-drawer" data-search-drawer>
    <div class="kb-search-backdrop" data-search-close></div>
    <div class="kb-search-panel">
      <div class="kb-search-head">
        <div class="kb-search-title">Search the Atelier</div>
        <button class="kb-search-close" type="button" aria-label="Close search" data-search-close>&times;</button>
      </div>
      <form class="kb-search-form" action="{{ route('search') }}" method="GET">
        <input type="search" name="q" placeholder="Search products, collections, colors..." autocomplete="off">
        <button class="kb-btn-primary" type="submit">Search</button>
      </form>
      <div class="kb-search-grid">
        <div>
          <div class="kb-search-label">Popular Searches</div>
          <div class="kb-search-tags">
            <a href="{{ route('search', ['q' => 'Winter']) }}">Winter</a>
            <a href="{{ route('search', ['q' => 'Men']) }}">Men</a>
            <a href="{{ route('search', ['q' => 'Women']) }}">Women</a>
            <a href="{{ route('search', ['q' => 'Oxford']) }}">Oxford</a>
            <a href="{{ route('search', ['q' => 'Jasper']) }}">Jasper</a>
          </div>
        </div>
        <div>
          <div class="kb-search-label">Quick Collections</div>
          <div class="kb-search-links">
            <a href="{{ route('collections.show', ['slug' => 'men-all']) }}">Men All</a>
            <a href="{{ route('collections.show', ['slug' => 'women-all']) }}">Women All</a>
            <a href="{{ route('collections.show', ['slug' => 'winter25']) }}">Winter '25</a>
            <a href="{{ route('collections.show', ['slug' => '12-12-sale']) }}">12.12 Sale</a>
          </div>
        </div>
      </div>
    </div>
  </div>

  @yield('content')

  @include('partials.footer')

  <div class="modal fade" id="kbQuickView" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-body p-0">
          <div class="row g-0">
            <div class="col-md-6">
              <img class="w-100 h-100" style="object-fit:cover; min-height:320px;" data-quick-image alt="Product preview">
            </div>
            <div class="col-md-6 p-4">
              <h5 class="mb-2" data-quick-title>Product</h5>
              <div class="text-muted mb-3" data-quick-price>{{ \App\Support\CurrencyFormatter::format(0) }}</div>
              <p class="mb-3" data-quick-desc></p>
              <div class="d-flex gap-2 flex-wrap">
                <button class="kb-btn-primary js-cart" type="button" data-quick-cart data-product-id="">Add to Cart</button>
                <a class="kb-btn-outline text-decoration-none" data-quick-link href="#" role="button">View Details</a>
                <button class="kb-btn-outline" type="button" data-bs-dismiss="modal">Close</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    document.querySelectorAll('[data-slider]').forEach(function (slider) {
      var track = slider.querySelector('.kb-slider-track');
      if (!track) {
        return;
      }

      var getStep = function () {
        var slide = track.querySelector('.kb-slide');
        if (!slide) {
          return 0;
        }
        var style = window.getComputedStyle(track);
        var gap = parseFloat(style.gap || style.columnGap || '16');
        return slide.getBoundingClientRect().width + gap;
      };

      var timer;
      var stop = function () {
        if (timer) {
          clearInterval(timer);
          timer = null;
        }
      };
      var scrollByStep = function (delta) {
        var step = getStep();
        if (!step) {
          return;
        }
        if (delta < 0) {
          if (track.scrollLeft <= 1) {
            track.scrollTo({ left: track.scrollWidth - track.clientWidth, behavior: 'smooth' });
          } else {
            track.scrollBy({ left: -step, behavior: 'smooth' });
          }
          return;
        }
        var next = track.scrollLeft + step;
        if (next + track.clientWidth >= track.scrollWidth - 1) {
          track.scrollTo({ left: 0, behavior: 'smooth' });
        } else {
          track.scrollBy({ left: step, behavior: 'smooth' });
        }
      };
      var start = function () {
        stop();
        if (track.scrollWidth <= track.clientWidth + 2) {
          return;
        }
        timer = setInterval(function () {
          scrollByStep(1);
        }, 3000);
      };

      var prev = slider.querySelector('.kb-slider-prev');
      var next = slider.querySelector('.kb-slider-next');
      if (prev) {
        prev.addEventListener('click', function () {
          stop();
          scrollByStep(-1);
          start();
        });
      }
      if (next) {
        next.addEventListener('click', function () {
          stop();
          scrollByStep(1);
          start();
        });
      }

      slider.addEventListener('mouseenter', stop);
      slider.addEventListener('mouseleave', start);
      start();
    });

    document.querySelectorAll('[data-tab-target]').forEach(function (tab) {
      tab.addEventListener('click', function () {
        var target = tab.getAttribute('data-tab-target');
        document.querySelectorAll('[data-tab-target]').forEach(function (btn) {
          btn.classList.remove('active');
        });
        tab.classList.add('active');

        document.querySelectorAll('[data-tab-panel]').forEach(function (panel) {
          panel.classList.toggle('d-none', panel.getAttribute('data-tab-panel') !== target);
        });
      });
    });

    var filterToggles = document.querySelectorAll('[data-filter-toggle]');
    filterToggles.forEach(function (toggle) {
      toggle.addEventListener('click', function () {
        var key = toggle.getAttribute('data-filter-toggle');
        var panel = document.querySelector('[data-filter-panel=\"' + key + '\"]');
        if (!panel) {
          return;
        }
        var isOpen = panel.classList.toggle('is-open');
        toggle.classList.toggle('is-open', isOpen);
        toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
      });
    });

    var grid = document.querySelector('[data-product-grid]');
    if (grid) {
      document.querySelectorAll('[data-view-btn]').forEach(function (btn) {
        btn.addEventListener('click', function () {
          var view = btn.getAttribute('data-view-btn');
          document.querySelectorAll('[data-view-btn]').forEach(function (item) {
            item.classList.remove('active');
          });
          btn.classList.add('active');
          grid.classList.toggle('is-list', view === 'list');
        });
      });
    }

    var sort = document.querySelector('[data-sort]');
    if (sort && grid) {
      var sortToggle = sort.querySelector('[data-sort-toggle]');
      var sortMenu = sort.querySelector('.kb-sort-menu');
      var sortLabel = sort.querySelector('[data-sort-label]');
      var sortOptions = sort.querySelectorAll('[data-sort-option]');
      var originalOrder = Array.from(grid.querySelectorAll('.kb-product-col'));

      var closeSort = function () {
        sort.classList.remove('is-open');
        if (sortToggle) {
          sortToggle.setAttribute('aria-expanded', 'false');
        }
      };
      var openSort = function () {
        sort.classList.add('is-open');
        if (sortToggle) {
          sortToggle.setAttribute('aria-expanded', 'true');
        }
      };
      var applySort = function (key) {
        var items = Array.from(grid.querySelectorAll('.kb-product-col'));
        if (key === 'best') {
          items = originalOrder.slice();
        } else if (key === 'price-asc') {
          items.sort(function (a, b) {
            return parseFloat(a.dataset.priceValue || '0') - parseFloat(b.dataset.priceValue || '0');
          });
        } else if (key === 'price-desc') {
          items.sort(function (a, b) {
            return parseFloat(b.dataset.priceValue || '0') - parseFloat(a.dataset.priceValue || '0');
          });
        } else if (key === 'title') {
          items.sort(function (a, b) {
            return (a.dataset.title || '').localeCompare(b.dataset.title || '');
          });
        } else if (key === 'new') {
          items.sort(function (a, b) {
            return parseInt(b.dataset.created || '0', 10) - parseInt(a.dataset.created || '0', 10);
          });
        }
        items.forEach(function (item) {
          grid.appendChild(item);
        });
      };

      if (sortToggle) {
        sortToggle.addEventListener('click', function () {
          if (sort.classList.contains('is-open')) {
            closeSort();
          } else {
            openSort();
          }
        });
      }
      sortOptions.forEach(function (option) {
        option.addEventListener('click', function () {
          var key = option.getAttribute('data-sort-option') || 'best';
          applySort(key);
          if (sortLabel) {
            sortLabel.textContent = option.textContent;
          }
          sortOptions.forEach(function (item) {
            item.classList.remove('is-active');
          });
          option.classList.add('is-active');
          closeSort();
        });
      });
      document.addEventListener('click', function (event) {
        if (!sort.contains(event.target)) {
          closeSort();
        }
      });
      document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
          closeSort();
        }
      });
    }

    var priceFilter = document.querySelector('.kb-price-filter');
    if (priceFilter) {
      var minRange = priceFilter.querySelector('[data-price-min-range]');
      var maxRange = priceFilter.querySelector('[data-price-max-range]');
      var priceSelected = priceFilter.querySelector('[data-price-selected]');
      var priceFeedback = priceFilter.querySelector('[data-price-feedback]');
      var resetBtn = priceFilter.querySelector('[data-price-reset]');
      var resultsCount = document.querySelector('[data-results-count]');
      var filterAlert = document.querySelector('[data-filter-alert]');
      var symbol = priceFilter.getAttribute('data-price-symbol') || '';
      var priceCards = document.querySelectorAll('[data-price-value]');
      var activeHandle = 'max';

      var formatValue = function (value) {
        var number = Number(value || 0);
        return symbol + ' ' + number.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
      };

      if (minRange && maxRange) {
        var minLimit = parseFloat(minRange.min || '0');
        var maxLimit = parseFloat(maxRange.max || '0');
        var prices = [];
        priceCards.forEach(function (card) {
          var price = parseFloat(card.getAttribute('data-price-value') || '0');
          if (!isNaN(price)) {
            prices.push(price);
          }
        });
        var datasetMin = prices.length ? Math.min.apply(null, prices) : minLimit;
        var datasetMax = prices.length ? Math.max.apply(null, prices) : maxLimit;

        var updateTrack = function (minValue, maxValue) {
          var range = maxLimit - minLimit || 1;
          var minPercent = ((minValue - minLimit) / range) * 100;
          var maxPercent = ((maxValue - minLimit) / range) * 100;
          var gradient = 'linear-gradient(90deg, #e5e5e5 ' + minPercent + '%, #111 ' + minPercent + '%, #c23b2a ' + maxPercent + '%, #e5e5e5 ' + maxPercent + '%)';
          minRange.style.background = gradient;
          maxRange.style.background = gradient;
        };

        var minGap = parseFloat(minRange.step || '1');

        var applyFilter = function () {
          var minValue = parseFloat(minRange.value || '0');
          var maxValue = parseFloat(maxRange.value || '0');
          if (maxValue - minValue < minGap) {
            if (activeHandle === 'min') {
              minValue = maxValue - minGap;
            } else {
              maxValue = minValue + minGap;
            }
          }
          minValue = Math.max(minLimit, minValue);
          maxValue = Math.min(maxLimit, maxValue);
          minRange.value = minValue;
          maxRange.value = maxValue;
          if (priceSelected) {
            priceSelected.textContent = formatValue(minValue) + ' - ' + formatValue(maxValue);
          }
          updateTrack(minValue, maxValue);
          var visible = 0;
          priceCards.forEach(function (card) {
            var price = parseFloat(card.getAttribute('data-price-value') || '0');
            var hidden = price < minValue || price > maxValue;
            card.classList.toggle('d-none', hidden);
            if (!hidden) {
              visible += 1;
            }
          });
          if (resultsCount) {
            resultsCount.textContent = String(visible);
          }
          if (visible === 0) {
            var hint = 'No products in this price range. Try ' + formatValue(datasetMin) + ' - ' + formatValue(datasetMax) + '.';
            if (priceFeedback) {
              priceFeedback.textContent = hint;
            }
            if (filterAlert) {
              filterAlert.textContent = hint;
              filterAlert.classList.remove('d-none');
            }
          } else {
            if (priceFeedback) {
              priceFeedback.textContent = '';
            }
            if (filterAlert) {
              filterAlert.classList.add('d-none');
            }
          }
        };

        minRange.addEventListener('input', function () {
          activeHandle = 'min';
          applyFilter();
        });
        maxRange.addEventListener('input', function () {
          activeHandle = 'max';
          applyFilter();
        });
        if (resetBtn) {
          resetBtn.addEventListener('click', function () {
            minRange.value = minLimit;
            maxRange.value = maxLimit;
            applyFilter();
          });
        }
        applyFilter();
      }
    }

    var wishlist = JSON.parse(localStorage.getItem('kbWishlist') || '[]');
    var cartItems = JSON.parse(localStorage.getItem('kbCartItems') || '[]');
    var cartCount = cartItems.length;
    var clearCart = document.querySelector('[data-clear-cart]');
    if (clearCart) {
      cartItems = [];
      cartCount = 0;
      localStorage.setItem('kbCartItems', JSON.stringify(cartItems));
      localStorage.setItem('kbCartCount', '0');
    }
    if (!cartCount) {
      localStorage.setItem('kbCartCount', '0');
    }
    var wishlistCountEls = document.querySelectorAll('[data-wishlist-count]');
    var cartCountEls = document.querySelectorAll('[data-cart-count]');

    var updateCounts = function () {
      wishlistCountEls.forEach(function (el) {
        el.textContent = wishlist.length;
        el.style.display = wishlist.length ? 'inline-block' : 'none';
      });
      cartCountEls.forEach(function (el) {
        el.textContent = cartCount;
        el.style.display = cartCount ? 'inline-block' : 'none';
      });
      document.querySelectorAll('[data-wishlist-total]').forEach(function (el) {
        el.textContent = wishlist.length + ' items saved';
      });
    };

    document.querySelectorAll('.js-wishlist').forEach(function (btn) {
      var id = btn.getAttribute('data-product-id');
      if (wishlist.includes(id)) {
        btn.classList.add('active');
      }

      btn.addEventListener('click', function () {
        if (wishlist.includes(id)) {
          wishlist = wishlist.filter(function (item) { return item !== id; });
          btn.classList.remove('active');
        } else {
          wishlist.push(id);
          btn.classList.add('active');
        }
        localStorage.setItem('kbWishlist', JSON.stringify(wishlist));
        updateCounts();
      });
    });

    document.querySelectorAll('.js-cart').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var id = btn.getAttribute('data-product-id');
        if (id) {
          cartItems.push(id);
          localStorage.setItem('kbCartItems', JSON.stringify(cartItems));
        }
        cartCount = cartItems.length;
        localStorage.setItem('kbCartCount', String(cartCount));
        updateCounts();
      });
    });

    document.querySelectorAll('.js-zoom').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var modalEl = document.getElementById('kbQuickView');
        if (!modalEl || typeof bootstrap === 'undefined') {
          return;
        }
        modalEl.querySelector('[data-quick-title]').textContent = btn.getAttribute('data-title') || 'Product';
        modalEl.querySelector('[data-quick-price]').textContent = btn.getAttribute('data-price') || '';
        modalEl.querySelector('[data-quick-desc]').textContent = btn.getAttribute('data-description') || '';
        var image = btn.getAttribute('data-image') || '';
        modalEl.querySelector('[data-quick-image]').setAttribute('src', image);
        var link = btn.getAttribute('data-url') || '#';
        var linkEl = modalEl.querySelector('[data-quick-link]');
        if (linkEl) {
          linkEl.setAttribute('href', link);
        }
        var cartBtn = modalEl.querySelector('[data-quick-cart]');
        if (cartBtn) {
          cartBtn.setAttribute('data-product-id', btn.getAttribute('data-product-id') || '');
        }
        var modal = new bootstrap.Modal(modalEl);
        modal.show();
      });
    });

    updateCounts();

    var fetchProducts = function (handles, callback) {
      if (!handles || !handles.length) {
        callback([], {});
        return;
      }
      var url = '/api/products?handles=' + encodeURIComponent(handles.join(','));
      fetch(url)
        .then(function (response) { return response.json(); })
        .then(function (payload) { callback(payload.items || [], payload || {}); })
        .catch(function () { callback([], {}); });
    };

    var wishlistPage = document.querySelector('[data-wishlist-page]');
    if (wishlistPage) {
      var wishlistList = wishlistPage.querySelector('[data-wishlist-list]');
      var wishlistEmpty = wishlistPage.querySelector('[data-wishlist-empty]');
      var wishlistClear = wishlistPage.querySelector('[data-wishlist-clear]');
      if (wishlistClear) {
        wishlistClear.addEventListener('click', function () {
          wishlist = [];
          localStorage.setItem('kbWishlist', JSON.stringify(wishlist));
          updateCounts();
          if (wishlistList) {
            wishlistList.innerHTML = '';
          }
          if (wishlistEmpty) {
            wishlistEmpty.classList.remove('d-none');
          }
        });
      }
      if (wishlist.length) {
        if (wishlistEmpty) {
          wishlistEmpty.classList.add('d-none');
        }
        fetchProducts(wishlist, function (items) {
          wishlistList.innerHTML = items.map(function (item) {
            return '' +
              '<div class=\"col-6 col-md-4 col-lg-3\">' +
                '<div class=\"kb-product-card position-relative\">' +
                  '<div class=\"kb-product-media\">' +
                    '<a class=\"text-decoration-none text-dark\" href=\"' + item.url + '\">' +
                      (item.image ? '<img class=\"kb-product-img kb-product-img--main kb-ratio-tall\" src=\"' + item.image + '\" alt=\"' + item.title + '\">' : '<div class=\"kb-ph kb-ratio-tall no-label\"></div>') +
                    '</a>' +
                    '<div class=\"kb-product-actions\">' +
                      '<button class=\"kb-action-btn js-cart\" type=\"button\" data-product-id=\"' + item.handle + '\" aria-label=\"Add to cart\"><i class=\"bi bi-bag\"></i></button>' +
                      '<button class=\"kb-action-btn kb-remove\" type=\"button\" data-remove-wishlist=\"' + item.handle + '\" aria-label=\"Remove\"><i class=\"bi bi-x\"></i></button>' +
                    '</div>' +
                  '</div>' +
                  '<div class=\"name\">' + item.title + '</div>' +
                  '<div class=\"price\">' + item.price_label + '</div>' +
                '</div>' +
              '</div>';
          }).join('');

          wishlistList.querySelectorAll('[data-remove-wishlist]').forEach(function (btn) {
            btn.addEventListener('click', function () {
              var id = btn.getAttribute('data-remove-wishlist');
              wishlist = wishlist.filter(function (item) { return item !== id; });
              localStorage.setItem('kbWishlist', JSON.stringify(wishlist));
              updateCounts();
              btn.closest('.col-6').remove();
              if (!wishlist.length && wishlistEmpty) {
                wishlistEmpty.classList.remove('d-none');
              }
            });
          });
        });
      } else if (wishlistEmpty) {
        wishlistEmpty.classList.remove('d-none');
      }
    }

    var cartPage = document.querySelector('[data-cart-page]');
    if (cartPage) {
      var cartList = cartPage.querySelector('[data-cart-list]');
      var cartEmpty = cartPage.querySelector('[data-cart-empty]');
      var cartContent = cartPage.querySelector('[data-cart-content]');
      var cartSubtotal = cartPage.querySelector('[data-cart-subtotal]');
      var cartTotal = cartPage.querySelector('[data-cart-total]');
      var cartData = { items: [], payload: {} };
      var cartClear = cartPage.querySelector('[data-cart-clear]');
      if (cartClear) {
        cartClear.addEventListener('click', function () {
          cartItems = [];
          localStorage.setItem('kbCartItems', JSON.stringify(cartItems));
          cartCount = 0;
          localStorage.setItem('kbCartCount', '0');
          updateCounts();
          renderCart();
        });
      }

      var renderCart = function () {
        var counts = cartItems.reduce(function (acc, id) {
          acc[id] = (acc[id] || 0) + 1;
          return acc;
        }, {});
        var items = cartData.items.filter(function (item) { return counts[item.handle]; });
        var symbol = cartData.payload.symbol || '';
        var total = 0;

        if (!items.length) {
          if (cartEmpty) {
            cartEmpty.classList.remove('d-none');
          }
          if (cartContent) {
            cartContent.classList.add('d-none');
          }
          return;
        }

        if (cartEmpty) {
          cartEmpty.classList.add('d-none');
        }
        if (cartContent) {
          cartContent.classList.remove('d-none');
        }

        cartList.innerHTML = items.map(function (item) {
          var qty = counts[item.handle] || 1;
          var lineTotal = (item.price_value || 0) * qty;
          total += lineTotal;
          return '' +
            '<div class=\"kb-cart-item\">' +
              (item.image ? '<img src=\"' + item.image + '\" alt=\"' + item.title + '\">' : '<div class=\"kb-cart-thumb\"></div>') +
              '<div class=\"kb-cart-info\">' +
                '<div class=\"kb-cart-title\">' + item.title + '</div>' +
                '<div class=\"kb-cart-price\">' + item.price_label + '</div>' +
                '<div class=\"kb-cart-qty\">' +
                  '<span>Qty</span>' +
                  '<div class=\"kb-cart-qty-control\">' +
                    '<button type=\"button\" data-cart-dec=\"' + item.handle + '\">-</button>' +
                    '<span>' + qty + '</span>' +
                    '<button type=\"button\" data-cart-inc=\"' + item.handle + '\">+</button>' +
                  '</div>' +
                '</div>' +
              '</div>' +
              '<div class=\"kb-cart-actions\">' +
                '<button class=\"kb-btn-outline kb-remove\" type=\"button\" data-remove-cart=\"' + item.handle + '\">Remove</button>' +
              '</div>' +
            '</div>';
        }).join('');

        var formattedTotal = symbol
          ? symbol + ' ' + total.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })
          : total.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        if (cartSubtotal) {
          cartSubtotal.textContent = formattedTotal;
        }
        if (cartTotal) {
          cartTotal.textContent = formattedTotal;
        }

        cartList.querySelectorAll('[data-remove-cart]').forEach(function (btn) {
          btn.addEventListener('click', function () {
            var id = btn.getAttribute('data-remove-cart');
            cartItems = cartItems.filter(function (item) { return item !== id; });
            localStorage.setItem('kbCartItems', JSON.stringify(cartItems));
            cartCount = cartItems.length;
            localStorage.setItem('kbCartCount', String(cartCount));
            updateCounts();
            renderCart();
          });
        });

        cartList.querySelectorAll('[data-cart-inc]').forEach(function (btn) {
          btn.addEventListener('click', function () {
            var id = btn.getAttribute('data-cart-inc');
            cartItems.push(id);
            localStorage.setItem('kbCartItems', JSON.stringify(cartItems));
            cartCount = cartItems.length;
            localStorage.setItem('kbCartCount', String(cartCount));
            updateCounts();
            renderCart();
          });
        });

        cartList.querySelectorAll('[data-cart-dec]').forEach(function (btn) {
          btn.addEventListener('click', function () {
            var id = btn.getAttribute('data-cart-dec');
            var index = cartItems.indexOf(id);
            if (index > -1) {
              cartItems.splice(index, 1);
              localStorage.setItem('kbCartItems', JSON.stringify(cartItems));
              cartCount = cartItems.length;
              localStorage.setItem('kbCartCount', String(cartCount));
              updateCounts();
              renderCart();
            }
          });
        });
      };

      if (cartItems.length) {
        var counts = cartItems.reduce(function (acc, id) {
          acc[id] = (acc[id] || 0) + 1;
          return acc;
        }, {});
        var handles = Object.keys(counts);
        fetchProducts(handles, function (items, payload) {
          cartData.items = items || [];
          cartData.payload = payload || {};
          renderCart();
        });
      } else if (cartEmpty) {
        cartEmpty.classList.remove('d-none');
        if (cartContent) {
          cartContent.classList.add('d-none');
        }
      }
    }

    var checkoutPage = document.querySelector('[data-checkout-page]');
    if (checkoutPage) {
      var checkoutList = checkoutPage.querySelector('[data-checkout-list]');
      var checkoutSubtotal = checkoutPage.querySelector('[data-checkout-subtotal]');
      var checkoutTotal = checkoutPage.querySelector('[data-checkout-total]');
      var checkoutForm = checkoutPage.querySelector('.kb-checkout-form');
      var cartPayloadInput = checkoutPage.querySelector('[data-cart-payload]');
      var paymentOptions = checkoutPage.querySelectorAll('[data-payment-option]');
      var paymentPanels = checkoutPage.querySelectorAll('[data-payment-panel]');
      var demoShippingBtn = checkoutPage.querySelector('[data-demo-shipping]');
      var demoCardBtn = checkoutPage.querySelector('[data-demo-card]');
      var demoBankBtn = checkoutPage.querySelector('[data-demo-bank]');

      var setPaymentPanel = function (value) {
        paymentPanels.forEach(function (panel) {
          panel.classList.toggle('d-none', panel.getAttribute('data-payment-panel') !== value);
        });
        var cardFields = checkoutPage.querySelectorAll('[name=\"card_name\"], [name=\"card_number\"], [name=\"card_expiry\"], [name=\"card_cvc\"]');
        var bankFields = checkoutPage.querySelectorAll('[name=\"bank_name\"], [name=\"bank_account\"], [name=\"bank_reference\"], [name=\"payment_proof\"]');
        cardFields.forEach(function (field) {
          field.required = value === 'card';
        });
        bankFields.forEach(function (field) {
          field.required = value === 'bank';
        });
      };

      if (cartItems.length) {
        var counts = cartItems.reduce(function (acc, id) {
          acc[id] = (acc[id] || 0) + 1;
          return acc;
        }, {});
        if (cartPayloadInput) {
          cartPayloadInput.value = JSON.stringify(counts);
        }
        var handles = Object.keys(counts);
        fetchProducts(handles, function (items, payload) {
          var symbol = payload.symbol || '';
          var total = 0;
          checkoutList.innerHTML = items.map(function (item) {
            var qty = counts[item.handle] || 1;
            var lineTotal = (item.price_value || 0) * qty;
            total += lineTotal;
            return '' +
              '<div class=\"kb-checkout-item\">' +
                (item.image ? '<img src=\"' + item.image + '\" alt=\"' + item.title + '\">' : '<div class=\"kb-cart-thumb\"></div>') +
                '<div>' +
                  '<div class=\"kb-cart-title\">' + item.title + '</div>' +
                  '<div class=\"kb-cart-price\">' + item.price_label + '</div>' +
                  '<div class=\"kb-cart-qty\">Qty: <strong>' + qty + '</strong></div>' +
                '</div>' +
                '<div class=\"kb-cart-price\">' + (symbol ? symbol + ' ' : '') + lineTotal.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + '</div>' +
              '</div>';
          }).join('');

          var formattedTotal = symbol
            ? symbol + ' ' + total.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })
            : total.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
          if (checkoutSubtotal) {
            checkoutSubtotal.textContent = formattedTotal;
          }
          if (checkoutTotal) {
            checkoutTotal.textContent = formattedTotal;
          }
        });
      } else if (checkoutList) {
        checkoutList.innerHTML = '<div class=\"kb-empty-sub\">No items in cart. Add products before checkout.</div>';
      }

      if (paymentOptions.length) {
        paymentOptions.forEach(function (option) {
          option.addEventListener('change', function () {
            if (option.checked) {
              setPaymentPanel(option.value);
            }
          });
        });
        var active = checkoutPage.querySelector('[data-payment-option]:checked');
        if (active) {
          setPaymentPanel(active.value);
        }
      }

      if (demoShippingBtn) {
        demoShippingBtn.addEventListener('click', function () {
          var setValue = function (name, value) {
            var field = checkoutPage.querySelector('[name=\"' + name + '\"]');
            if (field) {
              field.value = value;
            }
          };
          setValue('customer_name', 'Ayesha Khan');
          setValue('email', 'ayesha.khan@example.com');
          setValue('phone', '+14375519575');
          setValue('city', 'Lahore');
          setValue('address', 'House 21, Gulberg');
          setValue('postal_code', '54000');
        });
      }

      if (demoCardBtn) {
        demoCardBtn.addEventListener('click', function () {
          var setValue = function (name, value) {
            var field = checkoutPage.querySelector('[name=\"' + name + '\"]');
            if (field) {
              field.value = value;
            }
          };
          setValue('card_name', 'Ayesha Khan');
          setValue('card_number', '4242 4242 4242 4242');
          setValue('card_expiry', '08/28');
          setValue('card_cvc', '123');
        });
      }

      if (demoBankBtn) {
        demoBankBtn.addEventListener('click', function () {
          var setValue = function (name, value) {
            var field = checkoutPage.querySelector('[name=\"' + name + '\"]');
            if (field) {
              field.value = value;
            }
          };
          setValue('bank_name', 'HBL');
          setValue('bank_account', 'PK00HBL0000000000000001');
          setValue('bank_reference', 'KB-TEST-001');
        });
      }

      if (checkoutForm && cartPayloadInput) {
        checkoutForm.addEventListener('submit', function (event) {
          if (!cartItems.length) {
            event.preventDefault();
            alert('Your cart is empty. Add products before placing an order.');
            return;
          }
          var payload = cartItems.reduce(function (acc, id) {
            acc[id] = (acc[id] || 0) + 1;
            return acc;
          }, {});
          cartPayloadInput.value = JSON.stringify(payload);
        });
      }
    }

    var searchDrawer = document.querySelector('[data-search-drawer]');
    var openSearch = function () {
      if (!searchDrawer) {
        return;
      }
      searchDrawer.classList.add('is-open');
      var input = searchDrawer.querySelector('input[type=\"search\"]');
      if (input) {
        setTimeout(function () {
          input.focus();
        }, 100);
      }
    };
    var closeSearch = function () {
      if (!searchDrawer) {
        return;
      }
      searchDrawer.classList.remove('is-open');
    };
    document.querySelectorAll('[data-search-open]').forEach(function (btn) {
      btn.addEventListener('click', openSearch);
    });
    document.querySelectorAll('[data-search-close]').forEach(function (btn) {
      btn.addEventListener('click', closeSearch);
    });
    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') {
        closeSearch();
      }
    });

    var navToggle = document.querySelector('[data-nav-toggle]');
    var nav = document.querySelector('.kb-nav');
    var navOverlay = document.querySelector('[data-nav-overlay]');
    var navCloseButtons = document.querySelectorAll('[data-nav-close]');
    var mobileNavQuery = window.matchMedia('(max-width: 992px)');

    var resetNavMenus = function () {
      if (!nav) {
        return;
      }
      nav.querySelectorAll('.is-open').forEach(function (item) {
        item.classList.remove('is-open');
      });
    };
    var closeNav = function () {
      document.body.classList.remove('kb-nav-open');
      if (navToggle) {
        navToggle.setAttribute('aria-expanded', 'false');
      }
      resetNavMenus();
    };
    var openNav = function () {
      document.body.classList.add('kb-nav-open');
      if (navToggle) {
        navToggle.setAttribute('aria-expanded', 'true');
      }
    };

    if (navToggle && nav) {
      navToggle.addEventListener('click', function () {
        if (document.body.classList.contains('kb-nav-open')) {
          closeNav();
        } else {
          openNav();
        }
      });
    }
    if (navOverlay) {
      navOverlay.addEventListener('click', closeNav);
    }
    navCloseButtons.forEach(function (btn) {
      btn.addEventListener('click', closeNav);
    });
    if (nav) {
      nav.addEventListener('click', function (event) {
        if (!mobileNavQuery.matches) {
          return;
        }
        var dropdownLink = event.target.closest('.kb-dropdown > .kb-nav-link');
        if (dropdownLink) {
          var dropdown = dropdownLink.closest('.kb-dropdown');
          if (dropdown && !dropdown.classList.contains('is-open')) {
            event.preventDefault();
            nav.querySelectorAll('.kb-dropdown.is-open').forEach(function (item) {
              if (item !== dropdown) {
                item.classList.remove('is-open');
              }
            });
            dropdown.classList.add('is-open');
          }
          return;
        }

        var submenuLink = event.target.closest('.kb-dropdown-list li.has-submenu > a');
        if (submenuLink) {
          var submenuItem = submenuLink.closest('.has-submenu');
          if (submenuItem && !submenuItem.classList.contains('is-open')) {
            event.preventDefault();
            var list = submenuItem.parentElement;
            if (list) {
              list.querySelectorAll('.has-submenu.is-open').forEach(function (item) {
                if (item !== submenuItem) {
                  item.classList.remove('is-open');
                }
              });
            }
            submenuItem.classList.add('is-open');
          }
          return;
        }

        var link = event.target.closest('a');
        if (link) {
          closeNav();
        }
      });
    }
    mobileNavQuery.addEventListener('change', function (event) {
      if (!event.matches) {
        closeNav();
      }
    });
    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') {
        closeNav();
      }
    });

    var mobileFilterToggle = document.querySelector('[data-mobile-filter-toggle]');
    var mobileFilterPanel = document.querySelector('[data-mobile-filter-panel]');
    if (mobileFilterToggle && mobileFilterPanel) {
      mobileFilterToggle.addEventListener('click', function () {
        var isOpen = mobileFilterPanel.classList.toggle('is-open');
        mobileFilterToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        if (isOpen && window.matchMedia('(max-width: 992px)').matches) {
          mobileFilterPanel.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
      });
    }
  </script>
  @stack('scripts')
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
