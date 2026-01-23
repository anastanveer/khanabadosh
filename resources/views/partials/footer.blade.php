<footer class="kb-footer">
  <div class="container">
    <div class="row g-4">

      <div class="col-12 col-md-3">
        <h6>KHANABADOSH</h6>
        <div class="small mb-2">
          Innisfil, Ontario<br>
          Canada
        </div>
        <div class="small">
          <strong>Phone:</strong> <a href="tel:+14375519575">+14375519575</a><br>
          <strong>Email:</strong> info@khanabadoshfashion.ca
        </div>
        <div class="mt-3 d-flex gap-2">
          <a href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
          <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
          <a href="#" aria-label="Youtube"><i class="bi bi-youtube"></i></a>
        </div>
      </div>

      <div class="col-6 col-md-2">
        <h6>Information</h6>
        <a href="{{ route('policy', ['title' => 'Shipping Policy']) }}">Shipping Policy</a><br>
        <a href="{{ route('policy', ['title' => 'Exchange & Return Policy']) }}">Exchange & Return Policy</a><br>
        <a href="{{ route('policy', ['title' => 'FAQs']) }}">FAQs</a><br>
        <a href="{{ route('policy', ['title' => 'Terms & Conditions']) }}">Terms & Conditions</a><br>
        <a href="{{ route('policy', ['title' => 'Privacy Policy']) }}">Privacy Policy</a>
      </div>

      <div class="col-6 col-md-2">
        <h6>Quick Shop</h6>
        <a href="{{ route('home') }}">Home</a><br>
        <a href="{{ route('collections.show', ['slug' => 'men-all']) }}">Catalog</a><br>
        <a href="#">Contact</a>
      </div>

      <div class="col-6 col-md-2">
        <h6>Customer Services</h6>
        <a href="#">Contact Us</a>
      </div>

      <div class="col-12 col-md-3">
        <h6>Newsletter</h6>
        <div class="small mb-2">Enter your email to receive daily news.</div>
        <form class="kb-newsletter d-flex gap-2">
          <input class="form-control" type="email" placeholder="Email address" />
          <button class="btn" type="button">SUBSCRIBE</button>
        </form>
      </div>

    </div>

    <hr class="my-4" style="border-color: rgba(255,255,255,.12)">

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 small">
      <div>© 2026 Khanabadosh. All Rights Reserved.</div>
      <div class="text-secondary">
        Designed by <a href="https://torontobytes.com/" target="_blank" rel="noopener">TorontoBytes</a>
      </div>
    </div>
  </div>
</footer>
