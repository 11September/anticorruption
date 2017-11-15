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

    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('voyager::multilingual.language-selector')
@stop

@section('content')

    <div class="page-content container-fluid">
        <div class="row">

            <div class="col-md-12">

                <div class="panel panel-bordered" style="padding-bottom:5px;">


                    <div class="panel-heading" style="border-bottom:0;">
                        <h3 class="panel-title">Название</h3>
                    </div>

                    <div class="panel-body" style="padding-top:0;">
                        <p>{{ $dataTypeContent->name }}</p>
                    </div>

                    <hr style="margin: 0">

                    <div class="panel-heading" style="border-bottom:0;">
                        <h3 class="panel-title">Адрес</h3>
                    </div>

                    <div class="panel-body" style="padding-top:0;">
                        <p>{{ $dataTypeContent->address }}</p>
                    </div>

                    <hr style="margin: 0">

                    <div class="panel-heading" style="border-bottom:0;">
                        <h3 class="panel-title">Город</h3>
                    </div>

                    <div class="panel-body" style="padding-top:0;">
                        <p>{{ $dataTypeContent->city->name }}</p>
                    </div>

                    <hr style="margin: 0">

                    <div class="panel-heading" style="border-bottom:0;">
                        <h3 class="panel-title">Категория</h3>
                    </div>

                    <div class="panel-body" style="padding-top:0;">
                        <p>{{ $dataTypeContent->category->name }}</p>
                    </div>

                    <hr style="margin: 0">

                    <div class="panel-heading" style="border-bottom:0;">
                        <h3 class="panel-title">Заказчик</h3>
                    </div>

                    <div class="panel-body" style="padding-top:0;">
                        <p>{{ $dataTypeContent->customer->name }}</p>
                    </div>

                    <hr style="margin: 0">

                    <div class="panel-heading" style="border-bottom:0;">
                        <h3 class="panel-title">Предприниматель</h3>
                    </div>

                    <div class="panel-body" style="padding-top:0;">
                        <p>{{ $dataTypeContent->contractor->name }}</p>
                    </div>

                    <hr style="margin: 0">

                    <div class="panel-heading" style="border-bottom:0;">
                        <h3 class="panel-title">Общая цена (грн.)</h3>
                    </div>

                    <div class="panel-body" style="padding-top:0;">
                        <p>{{ $dataTypeContent->price }}</p>
                    </div>

                    <hr style="margin: 0">

                    <div class="panel-heading" style="border-bottom:0;">
                        <h3 class="panel-title">Статус обьекта</h3>
                    </div>

                    <div class="panel-body" style="padding-top:0;">
                        <p>{{ $dataTypeContent->status }}</p>
                    </div>

                    <hr style="margin: 0">

                    <div class="panel-heading" style="border-bottom:0;">
                        <h3 class="panel-title">Описание</h3>
                    </div>

                    <div class="panel-body" style="padding-top:0;">
                        <p>{{ $dataTypeContent->description }}</p>
                    </div>

                    <hr style="margin: 0">

                    <div class="panel-heading" style="border-bottom:0;">
                        <h3 class="panel-title">Описание работ</h3>
                    </div>

                    <div class="panel-body" style="padding-top:0;">
                        <p>{{ $dataTypeContent->work_description }}</p>
                    </div>

                    <hr style="margin: 0">

                    <div class="panel-heading" style="border-bottom:0;">
                        <h3 class="panel-title">Дополнительная информация</h3>
                    </div>

                    <div class="panel-body" style="padding-top:0;">
                        <p>{{ $dataTypeContent->additional_info }}</p>
                    </div>

                    <hr style="margin: 0">

                    <div class="panel-heading" style="border-bottom:0;">
                        <h3 class="panel-title">Дата окончания работ</h3>
                    </div>

                    <div class="panel-body" style="padding-top:0;">
                        <p>{{ $dataTypeContent->finished_at }}</p>
                    </div>
                </div>

                @if($dataTypeContent->documents && count($dataTypeContent->documents) > 0)
                    <h2 class="text-center">
                        <a href="{{ url('admin/documents') }}" class="btn btn-warning">
                            Документы
                        </a>
                    </h2>

                    @foreach($dataTypeContent->documents as $document)
                        <div id="postlist">
                            <div class="panel">

                                <div class="panel-body">
                                    {{ $document->title }} - {{ $document->file_path }}
                                </div>

                                <div class="panel-footer">
                                    <span class="label label-default">{{ $document->created_at }}</span>
                                    @if (Voyager::can('delete_'."documents"))
                                        <a href="javascript:;" title="Delete"
                                           class="btn btn-sm btn-danger pull-right delete" data-action="remove_document"
                                           data-id="{{ $document->id }}"
                                           id="delete-{{ $document->id }}">
                                            <i class="voyager-trash"></i>
                                        </a>
                                    @endif
                                    <span class="pull-right read-more label label-default"><a href="{{ url('admin/documents', $document->id) }}">Подробнее</a></span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif


                @if($dataTypeContent->finances && count($dataTypeContent->finances) > 0)
                    <h2 class="text-center">
                        <a href="{{ url('admin/finances') }}" class="btn btn-warning">
                            Финансирование
                        </a>
                    </h2>

                    @foreach($dataTypeContent->finances as $finance)
                        <div id="postlist">
                            <div class="panel">

                                <div class="panel-body">
                                    <p>{{ $finance->suma }} - {{ $finance->status }} - {{ $finance->date }}</p>
                                    <p>{{ $finance->description }}</p>
                                </div>

                                <div class="panel-footer">
                                    <span class="label label-default">{{ $finance->created_at }}</span>
                                    @if (Voyager::can('delete_'."documents"))
                                        <a href="javascript:;" title="Delete"
                                           class="btn btn-sm btn-danger pull-right delete" data-action="remove_finances"
                                           data-id="{{ $finance->id }}"
                                           id="delete-{{ $finance->id }}">
                                            <i class="voyager-trash"></i>
                                        </a>
                                    @endif
                                    <span class="pull-right read-more label label-default"><a href="{{ url('admin/finances', $finance->id) }}">Подробнее</a></span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif


            </div>
        </div>
    </div>


    <div class="modal modal-danger fade" tabindex="-1" id="delete_modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><i class="voyager-trash"></i> Are you sure you want to delete
                        this {{ strtolower($dataType->display_name_singular) }}?</h4>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger pull-right delete-confirm" data-dismiss="modal">Yes,
                        delete this
                    </button>
                    <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancel</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
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
    <script>
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });


            var deleteFormAction;
            $('.delete').on('click', function (e) {
                e.preventDefault();

                var data = $(this).attr('data-id');
                var action = $(this).attr('data-action');
                var objective = $(this).closest('.panel');

//                Some Check

//                $('#delete_modal').modal('show');

//                Some Check

                $.ajax
                ({
                    type: 'post',
                    url: '/' + action,
                    data: {"id": data},
                    success: function (data) {
                        objective.remove();
                    },
                    statusCode: {
                        500: function () {
                            alert("Извините произошла ошибка!")
                        }
                    }
                });
            });


        });
    </script>
@stop
