@extends('layouts.app')

@section('content')
  <main class="kb-collection">
    <div class="container">
      <div class="kb-page-title">{{ $pageTitle }}</div>
      <div class="kb-page-sub">Khanabadosh Fashion Canada</div>
      <div class="kb-policy-body">
        <p>{{ $intro }}</p>

        @foreach ($sections as $section)
          <h3>{{ $section['title'] }}</h3>
          <ul>
            @foreach ($section['items'] as $item)
              <li>{{ $item }}</li>
            @endforeach
          </ul>
        @endforeach
      </div>
    </div>
  </main>
@endsection
