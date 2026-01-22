@extends('layouts.app')

@section('content')
  <main class="kb-collection" data-wishlist-page>
    <div class="container">
      <div class="kb-page-title">Wishlist</div>
      <div class="kb-page-sub">Curated picks you want to keep an eye on.</div>
      <div class="kb-wishlist-hero">
        <div>
          <div class="kb-wishlist-title">Saved Styles</div>
          <div class="kb-wishlist-sub">Add to cart anytime or keep discovering.</div>
        </div>
        <a class="kb-btn-outline text-decoration-none" href="{{ route('collections.show', ['slug' => 'men-all']) }}">Browse Collections</a>
      </div>

      <div class="kb-wishlist-toolbar">
        <div class="kb-wishlist-count" data-wishlist-total>0 items saved</div>
        <div class="kb-wishlist-actions">
          <button class="kb-btn-outline" type="button" data-wishlist-clear>Clear Wishlist</button>
          <a class="kb-btn-primary text-decoration-none text-white" href="{{ route('cart') }}">Go to Cart</a>
        </div>
      </div>

      <div class="kb-empty-state" data-wishlist-empty>
        <div class="kb-empty-title">No items in wishlist yet.</div>
        <div class="kb-empty-sub">Tap the heart on any product to save it here.</div>
        <a class="kb-btn-primary mt-3" href="{{ route('collections.show', ['slug' => 'men-all']) }}">Browse Collection</a>
      </div>

      <div class="row g-4 mt-2" data-wishlist-list></div>
    </div>
  </main>
@endsection
