@extends('voyager::master')

@section('page_title','All '.$dataType->display_name_plural)

@section('page_header')

    <style>
        #bulk_delete_btn {
            margin-left: 20px;
        }

        .page-title {
            font-size: 18px;
            margin-top: 3px;
            padding-top: 28px;
            color: #555;
            position: relative;
            padding-left: 75px;
        }

        .wrapper-main-objects-buttons {
            margin-bottom: 20px;
        }

        .voyager-double-up {
            vertical-align: text-top;
            padding: 0 5px;
        }

        .voyager-search {
            vertical-align: text-top;
            padding-right: 5px;
        }

        .export-admin-button {
            margin-left: 20px;
        }

        .btn-file input[type=file] {
            /*width: 100px;*/
            height: 36px;
            position: absolute;
            top: 0;
            right: 0;
            min-width: 100%;
            min-height: 100%;
            font-size: 100px;
            text-align: right;
            filter: alpha(opacity=0);
            opacity: 0;
            outline: none;
            background: white;
            cursor: inherit;
            display: block;
        }
    </style>

    <h1 class="page-title">
        <i class="{{ $dataType->icon }}"></i> {{ $dataType->display_name_plural }}
        @if (Voyager::can('add_'.$dataType->name))
            <a href="{{ route('voyager.'.$dataType->slug.'.create') }}" class="btn btn-success">
                <i class="voyager-plus"></i> Додати новий об'єкт
            </a>
        @endif

        @can('delete',app($dataType->model_name))
            @include('voyager::partials.bulk-delete')
        @endcan
    </h1>

    @include('voyager::multilingual.language-selector')
@stop

@section('content')
    <div class="page-content container-fluid">
        @include('voyager::alerts')

        <div class="row">
            <div class="col-md-12">
                <div class="wrapper-main-objects-buttons">

                    <div class="pull-right">
                        <a class="export-admin-button btn btn-warning" href="{{ url('export-download/admin') }}">
                            <i class="voyager-double-up"></i>Експортувати об'єкти<i class="voyager-double-up"></i>
                        </a>
                    </div>

                    <div class="pull-right">
                        <form style="display: inline-flex;" method="post" class="form-inline"
                              action="{{ action('ObjectsController@importObjectsDatabase') }}"
                              enctype="multipart/form-data">
                            {{ csrf_field() }}

                            <div class="form-group" style="margin-right: 10px;">
                    <span class="btn btn-default btn-file glyphicon">
                        <i class="voyager-search"></i>
                        Oгляд файлу<input name="file" type="file">
                    </span>
                            </div>

                            <div class="form-group">
                                <input type="submit" class="btn btn-info" value="Iмпортувати">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body table-responsive">
                        <table id="dataTable" class="row table table-hover">
                            <thead>
                            <tr>
                                <th></th>
                                @foreach($dataType->browseRows as $row)
                                    <th>
                                        @if ($isServerSide)
                                            <a href="{{ $row->sortByUrl() }}">
                                                @endif
                                                {{ $row->display_name }}
                                                @if ($isServerSide)
                                                    @if ($row->isCurrentSortField())
                                                        @if (!isset($_GET['sort_order']) || $_GET['sort_order'] == 'asc')
                                                            <i class="voyager-angle-up pull-right"></i>
                                                        @else
                                                            <i class="voyager-angle-down pull-right"></i>
                                                        @endif
                                                    @endif
                                            </a>
                                        @endif
                                    </th>
                                @endforeach
                                <th class="actions">{{ __('voyager.generic.actions') }}</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($dataTypeContent as $object)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="row_id" id="checkbox_{{ $object->id }}"
                                               value="{{ $object->id }}">
                                    </td>
                                    <td>
                                        {{ $object->name }}
                                    </td>
                                    <td>
                                        {{ $object->address }}
                                    </td>
                                    {{--<td>--}}
                                        {{--@if(isset($object->category->name))--}}
                                            {{--{{ $object->category->name }}--}}
                                        {{--@endif--}}
                                    {{--</td>--}}
                                    {{--<td>--}}
                                        {{--{{ $object->price }}--}}
                                    {{--</td>--}}
                                    <td>
                                        {{ $object->created_at }}
                                    </td>

                                    <td class="no-sort no-click" id="bread-actions">
                                        @if (Voyager::can('delete_'.$dataType->name))
                                            <a href="javascript:;" title="Delete"
                                               class="btn btn-sm btn-danger pull-right delete"
                                               data-id="{{ $object->id }}" id="delete-{{ $object->id }}">
                                                <i class="voyager-trash"></i> <span
                                                        class="hidden-xs hidden-sm">Видалити</span>
                                            </a>
                                        @endif
                                        @if (Voyager::can('edit_'.$dataType->name))
                                            <a href="{{ route('voyager.'.$dataType->slug.'.edit', $object->id) }}"
                                               title="Edit" class="btn btn-sm btn-primary pull-right edit">
                                                <i class="voyager-edit"></i> <span
                                                        class="hidden-xs hidden-sm">Редагувати</span>
                                            </a>
                                        @endif
                                        @if (Voyager::can('read_'.$dataType->name))
                                            <a href="{{ route('voyager.'.$dataType->slug.'.show', $object->id) }}"
                                               title="View" class="btn btn-sm btn-warning pull-right">
                                                <i class="voyager-eye"></i> <span
                                                        class="hidden-xs hidden-sm">Oгляд</span>
                                            </a>
                                        @endif
                                    </td>

                                </tr>
                            @endforeach

                            </tbody>
                        </table>
                        @if (isset($dataType->server_side) && $dataType->server_side)
                            <div class="pull-left">
                                <div role="status" class="show-res" aria-live="polite">
                                    Showing {{ $dataTypeContent->firstItem() }} to {{ $dataTypeContent->lastItem() }}
                                    of {{ $dataTypeContent->total() }} entries
                                </div>
                            </div>
                            <div class="pull-right">
                                {{ $dataTypeContent->links() }}
                            </div>
                        @endif
                    </div>
                </div>

                @can('delete',app($dataType->model_name))
                    @include('partials.bulk-delete')
                @endcan

            </div>
        </div>
    </div>

    <div class="modal modal-danger fade" tabindex="-1" id="delete_modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><i class="voyager-trash"></i> Ви впевнені, що хочете видалити
                        {{ strtolower($dataType->display_name_singular) }}?</h4>
                </div>
                <div class="modal-footer">
                    <form action="{{ route('voyager.'.$dataType->slug.'.index') }}" id="delete_form" method="POST">
                        {{ method_field("DELETE") }}
                        {{ csrf_field() }}
                        <input type="submit" class="btn btn-danger pull-right delete-confirm"
                               value="Так, вилучіть це {{ strtolower($dataType->display_name_singular) }}">
                    </form>
                    <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Скасувати</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@stop

@section('css')
    @if(!$dataType->server_side && config('dashboard.data_tables.responsive'))
        <link rel="stylesheet" href="{{ voyager_asset('lib/css/responsive.dataTables.min.css') }}">
    @endif
@stop





@section('javascript')
    <!-- DataTables -->
    @if(!$dataType->server_side && config('dashboard.data_tables.responsive'))
        <script src="{{ voyager_asset('lib/js/dataTables.responsive.min.js') }}"></script>
    @endif
    @if($isModelTranslatable)
        <script src="{{ voyager_asset('js/multilingual.js') }}"></script>
    @endif
    <script>
        $(document).ready(function () {
                    @if (!$dataType->server_side)
            var table = $('#dataTable').DataTable({
                    "lengthMenu": [[10, 20, 100, 300], [10, 20, 100, 300]],
                    "order": []
                        @if(config('dashboard.data_tables.responsive')), responsive: true @endif
                });
            @endif

            @if ($isModelTranslatable)
            $('.side-body').multilingual();
            @endif
        });

        var deleteFormAction;
        $('td').on('click', '.delete', function (e) {
            var form = $('#delete_form')[0];

            if (!deleteFormAction) { // Save form action initial value
                deleteFormAction = form.action;
            }

            form.action = deleteFormAction.match(/\/[0-9]+$/)
                ? deleteFormAction.replace(/([0-9]+$)/, $(this).data('id'))
                : deleteFormAction + '/' + $(this).data('id');
            console.log(form.action);

            $('#delete_modal').modal('show');
        });
    </script>
@stop
