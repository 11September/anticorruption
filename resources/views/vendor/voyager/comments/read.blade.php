@extends('voyager::master')

@section('page_title','View '.$dataType->display_name_singular)

@section('page_header')
    <h1 class="page-title">
        <i class="{{ $dataType->icon }}"></i> Viewing {{ ucfirst($dataType->display_name_singular) }} &nbsp;

        @if (Voyager::can('edit_'.$dataType->name))
            <a href="{{ route('voyager.'.$dataType->slug.'.edit', $dataTypeContent->getKey()) }}" class="btn btn-info">
                <span class="glyphicon glyphicon-pencil"></span>&nbsp;
                Edit
            </a>
        @endif
        <a href="{{ route('voyager.'.$dataType->slug.'.index') }}" class="btn btn-warning">
            <span class="glyphicon glyphicon-list"></span>&nbsp;
            Return to List
        </a>
    </h1>
    @include('voyager::multilingual.language-selector')
@stop

@section('content')

    <div class="page-content container-fluid">
        <div class="row">

            <div class="col-md-12">

                <div class="panel panel-bordered" style="padding-bottom:5px;">

                    <div class="panel-heading" style="border-bottom:0;">
                        <h3 class="panel-title">Обьект</h3>
                    </div>

                    <div class="panel-body" style="padding-top:0;">
                        <p>{{ $dataTypeContent->object->name }}</p>
                    </div>

                    <hr style="margin: 0">

                    <div class="panel-heading" style="border-bottom:0;">
                        <h3 class="panel-title">Комментарий</h3>
                    </div>

                    <div class="panel-body" style="padding-top:0;">
                        <p>{{ $dataTypeContent->comment }}</p>
                    </div>

                    <hr style="margin: 0">

                    <div class="panel-heading" style="border-bottom:0;">
                        <h3 class="panel-title">Пользователь</h3>
                    </div>

                    <div class="panel-body" style="padding-top:0;">
                        <p>{{ $dataTypeContent->user->name }}</p>
                    </div>


                    <hr style="margin: 0">

                    <div class="panel-heading" style="border-bottom:0;">
                        <h3 class="panel-title">Статус</h3>
                    </div>

                    <div class="panel-body" style="padding-top:0;">
                        <p>{{ $dataTypeContent->status }}</p>
                    </div>

                    <hr style="margin: 0">

                    <div class="panel-heading" style="border-bottom:0;">
                        <h3 class="panel-title">Время создания</h3>
                    </div>

                    <div class="panel-body" style="padding-top:0;">
                        <p>{{ $dataTypeContent->created_at }}</p>
                    </div>

                </div>

            </div>
        </div>
    </div>
@stop

@section('javascript')
    @if ($isModelTranslatable)
        <script>
            $(document).ready(function () {
                $('.side-body').multilingual();
            });
        </script>

        <script src="{{ voyager_asset('js/multilingual.js') }}"></script>
    @endif
@stop
