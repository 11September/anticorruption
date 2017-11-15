<div class="row right-pad-null">
    <div class="col-lg-12 no-pad">
        <div class="sidebar-body height-69">
            <div class="objects-table clearfix">

                <div class="col-lg-12 objects-table-head">

                    @if(app('request')->input('filter'))

                        <span class="last-objects-head list-objects-label">
                            <a class="backToMainPage" href="{{ url('/') }}">
                                <span class="glyphicon glyphicon-menu-left back-link"></span>
                            </a>
                            Результати пошуку
                        </span>

                        <span class="last-objects-head hide selected-object-label">
                            <span class="glyphicon glyphicon-menu-left back-link" onclick="hideInfo()"></span>
                            <span class="selected-object-head" onclick="hideInfo()">
                            </span>
                        </span>

                    @else

                        <span class="last-objects-head list-objects-label">
                                Останні об’єкти
                        </span>

                        <span class="last-objects-head hide selected-object-label">
                            <span class="glyphicon glyphicon-menu-left back-link" onclick="hideInfo()"></span>
                            <span class="selected-object-head" onclick="hideInfo()">
                            </span>
                        </span>

                    @endif

                    <span class="glyphicon glyphicon-save export-ico" data-toggle="modal" data-target="#Modal1"></span>
                </div>

                <div class="col-lg-12 mobile-sidebar-controller">
                    <span class=" glyphicon glyphicon-menu-up down-obj "></span>
                    <span class=" glyphicon glyphicon-menu-down  up-obj hide"></span>
                </div>

                <div id="scroll-part" class="scroll-part zero-height">
                    <div id="one-object" class="col-lg-12 objects-table-body">
                        <ul class="object-list" id="object-one-list">
                            @if(isset($redirectObjectId))
                                <script defer>

                                    $(document).ready(function () {
                                        $('.pagination-links-div').addClass('hide');
                                        object_id = {!! $redirectObjectId !!}
                                        loadObjectInformation(object_id);
                                    });

                                </script>
                            @endif
                        </ul>
                    </div>

                    <div class="col-lg-12 objects-table-body">

                        <ul class="object-list" id="object-all-list">

                            @if( count($objects) )

                                @foreach($objects as $object)

                                    @if($loop->index == 10)
                                        @break
                                    @endif
                                    <span class="list-item-flex">
                                        <input class="hiidenIdObject" type="hidden" value="{{ $object->id }}">
                                        <img class="image" src="{{ asset('storage' . DIRECTORY_SEPARATOR  . $object->category->image) }}" alt="">
                                        <div class="object-info">
                                            <span class="object-info-p">{{ $object->name }}</span>
                                            <span class="object-info-p">{{ $object->address }}</span>
                                            <span class="object-info-p">{{ "Cплачено " . floor($object->price/1000) . " тис. грн." }}</span>
                                            <span class="hide lat">{{ $object->maps_lat }}</span>
                                            <span class="hide lng">{{ $object->maps_lng }}</span>
                                        </div>
                                    </span>
                                @endforeach
                            @else

                                <span class="list-item-flex">
                                    <div class="error-block" style="text-align: center; ">
                                        <span class="empty-search-message">Нічого не знайдено <img src="{{ asset('img/nothing_found.png') }}"> </span>
                                    </div>                                
                                </span>

                            @endif
                        </ul>

                        <div class="pagination-links-div
                                @if(count($objects) < 10)
                        {{ 'hide' }}
                        @endif
                                ">
                            <ul class="pagination">
                                <li><a id="pagination-ten" value="10" class="disabled-link"
                                       onclick="sidebarObjectsAmount(10)">10</a></li>
                                <li><a id="pagination-twenty" value="20" class=""
                                       onclick="sidebarObjectsAmount(20)">20</a></li>
                                <li><a id="pagination-fifty" value="50" class=""
                                       onclick="sidebarObjectsAmount(50)">50</a></li>
                            </ul>
                        </div>
                    </div>

                    <div id="comments-part" class="col-lg-12 comments-part">

                    </div>
                </div>
            </div>

            <div class="sidebar-controller">
                <img class="image arrow-right " src="{{ asset('img/arrow-right.png') }}" alt="">
                <img class="image arrow-left hide" src="{{ asset('img/arrow-left.png') }}" alt="">
            </div>
        </div>
    </div>
</div>
