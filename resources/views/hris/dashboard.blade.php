@extends('layouts.app')

@section('title','TalentFlow — Dashboard')

@section('content')
    {{-- The SPA injects content into #contentArea. We render an initial container. --}}
    <div id="hris-root">
        {{-- Content is rendered by the included JS (copied from the original HTML) --}}
    </div>
@endsection
