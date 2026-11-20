@extends('admin.layout.main')
@section('title', 'PageSpeed Checker | SEO Tools')
@section('addStyle')
<link rel="stylesheet" href="{{ URL::to('/public/assets/admin-pagespeed/pagespeed.css') }}?v={{ @filemtime(public_path('assets/admin-pagespeed/pagespeed.css')) }}">
@endsection
@section('content')

<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6"><h1 class="m-0">PageSpeed Checker</h1></div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Home</a></li>
            <li class="breadcrumb-item active">SEO Tools</li>
            <li class="breadcrumb-item active">PageSpeed Checker</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">
      @if(!$hasKey)
        <div class="alert alert-warning"><strong>API key missing.</strong> Add <code>PAGESPEED_API_KEY=…</code> to <code>.env</code> and run <code>php artisan config:clear</code>.</div>
      @endif
      <div id="psc" class="psc" aria-live="polite">
        <div class="psc-loading">Loading the checker…</div>
      </div>
    </div>
  </section>
</div>

@endsection
@section('addScript')
<script>
  window.PSC = {
    bases: @json($bases),
    groups: @json($groups),
    runUrl: @json(route('admin.pagespeed.run')),
    csrf: @json(csrf_token()),
    hasKey: @json($hasKey),
  };
</script>
<script src="{{ URL::to('/public/assets/admin-pagespeed/pagespeed.js') }}?v={{ @filemtime(public_path('assets/admin-pagespeed/pagespeed.js')) }}"></script>
@endsection
