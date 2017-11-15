@extends('partials.master')

@section('title')
    {{ $page->title }}
@endsection

@section('description')
    {{ $page->meta_description }}
@endsection

@section('keywords')
    {{ $page->meta_keywords }}
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('css/font-awesome.min.css') }}">
@endsection

@section('content')
    <div class="api-section">
        <div class="container">
            <div style="text-align: center;margin-top: 20px;">
                {!! $page->body !!}
            </div>
        </div>
    </div>


    @include('partials.footer')
@endsection

@section('scripts')

@endsection