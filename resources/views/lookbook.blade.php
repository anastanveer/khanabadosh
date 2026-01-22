@extends('layouts.app')

@section('content')
  <main class="kb-collection">
    <div class="container">
      <div class="kb-page-title">Lookbook</div>
      <div class="kb-page-sub">Khanabadosh Lookbook PDF</div>
      <div class="kb-lookbook-wrap">
        <iframe
          class="kb-lookbook-frame"
          src="{{ asset('pdfs/Khanabadosh_Khaddar_Collection.pdf') }}"
          title="Khanabadosh Lookbook"
        ></iframe>
      </div>
      <div class="kb-lookbook-actions">
        <a href="{{ asset('pdfs/Khanabadosh_Khaddar_Collection.pdf') }}" target="_blank" rel="noopener">
          Open PDF in new tab
        </a>
      </div>
    </div>
  </main>
@endsection
