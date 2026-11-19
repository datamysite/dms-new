{{--
    Generic service page: renders the rows of service_sections for this service.
    Each section's `content` is Blade source, rendered with the same variables the
    old view had ($service, $subservice, $mainservice, $subservices, $blogs, ...).
--}}
@extends('web.includes.master')

@php
    $dbPage = $subservice ?? $service;
    $dbSections = \App\Models\ServiceSection::where('service_id', $dbPage->id)
        ->where('is_active', 1)
        ->orderBy('sort_order')
        ->get()
        ->groupBy('area');
    $dbVars = \Illuminate\Support\Arr::only(get_defined_vars(), [
        'nav', 'service', 'subservice', 'mainservice', 'subservices', 'blogs', 'publications', 'web_drop', 'noPopup', 'errors',
    ]);
    $dbRender = function ($area) use ($dbSections, $dbVars) {
        $html = '';
        foreach ($dbSections->get($area, collect()) as $dbSection) {
            $html .= \Illuminate\Support\Facades\Blade::render($dbSection->content, $dbVars) . "\n";
        }
        return $html;
    };
@endphp

@if($dbSections->has('head'))
@section('addStyle')
{!! $dbRender('head') !!}
@endsection
@endif

@section('content')
<main class="{{ $dbPage->main_class ?: 'main' }}">
{!! $dbRender('main') !!}
</main>
{!! $dbRender('after_main') !!}
@endsection

@if($dbSections->has('scripts'))
@section('addScript')
{!! $dbRender('scripts') !!}
@endsection
@endif
