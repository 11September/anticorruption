<div class="search-section">
    <div class="container-fluid">
        <div class="row search-body hide">

            <form method="post" action="{{ action('ObjectsController@filter') }}">
                {{ csrf_field() }}

                <div class="first-col  in-map-search  col-xs-12 no-pad ">
                    <div>
                        <div class="form-group map-search">
                            <input value="{{ isset($_POST['address']) ? $_POST['address'] : '' }}" type="text" id="search"
                                   name="address" class="form-control"
                                   placeholder="Знайти на карті">

                            <div class="search-wrapper">
                                <button type="submit">
                                    <span class="glyphicon glyphicon-search"></span>
                                </button>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="second-col  filter-group  col-xs-12   five-pad">
                    <div class="select-group">
                        <div class="single-select first-select">
                            <label class="select-label" for="">Категорія</label>
     
                            <select data-live-search="true" title="Категорія"
                                    class="selectpicker show-menu-arrow form-control" name="category_id[]"
                                    multiple="multiple">
                               
                                @foreach($categories as $category)
                                    <option
                                            @if(isset($_POST['category_id']))
                                                @foreach($_POST['category_id'] as $key => $value)
                                                    @if($category->id == $value)
                                                        selected="selected"
                                                    @endif
                                                @endforeach
                                            @endif
                                            value="{{ $category->id }}"
                                            data-content="<img style='width: 20px;height:20px; position: relative; bottom: 2px;' src='{{ asset('storage' . DIRECTORY_SEPARATOR  . $category->image)}}'>{{ $category->name }}">
                                    </option>
                                @endforeach
                            </select>

                        </div>
                        <div class="single-select">
                            <label class="select-label" for="">Місто</label>

                            <select data-live-search="true" title="Місто"
                                    class="selectpicker show-menu-arrow form-control" name="city_id[]"
                                    multiple="multiple">
                                @foreach($cities as $city)
                                    <option @if(isset($_POST['city_id']))
                                                @foreach($_POST['city_id'] as $key => $value)
                                                    @if($city->id == $value)
                                                        selected="selected"
                                                    @endif
                                                @endforeach
                                            @endif value="{{ $city->id }}">{{ $city->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="third-col   filter-group  col-xs-12   five-pad">
                    <div class="select-group">
                        <div class="single-select first-select">
                            <label class="select-label" for="">Замовник</label>

                            <select data-live-search="true" title="Замовник"
                                    class="selectpicker show-menu-arrow form-control" name="customer_id[]"
                                    multiple="multiple">
                                @foreach($customers as $customer)
                                    <option
                                            @if(isset($_POST['customer_id']))
                                                @foreach($_POST['customer_id'] as $key => $value)
                                                    @if($customer->id == $value)
                                                        selected="selected"
                                                    @endif
                                                @endforeach
                                            @endif
                                            value="{{ $customer->id }}">{{ $customer->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="single-select">
                            <label class="select-label" for="">Підрядник</label>

                            <select data-live-search="true" title="Підрядник"
                                    class="selectpicker show-menu-arrow form-control" name="contractor_id[]"
                                    multiple="multiple">
                                @foreach($contractors as $contractor)
                                    <option
                                            @if(isset($_POST['contractor_id']))
                                                @foreach($_POST['contractor_id'] as $key => $value)
                                                    @if($contractor->id == $value)
                                                        selected="selected"
                                                    @endif
                                                @endforeach
                                            @endif
                                            value="{{ $contractor->id }}">{{ $contractor->name }}</option>
                                @endforeach
                            </select>

                        </div>
                    </div>
                </div>

                <div class="fourt-col  summ-filter  col-xs-12">
                    <div class="row summ-filter-row">
                        <div class="col-lg-8 col-md-8 col-sm-12 summ-block five-pad padding">
                            <div class="summ-slider">
                                <div class="form-inline">
                                    <div class="form-group summ-group-form">
                                        <label for="exampleInputName2" class="summ-label">Сума<span class="hide-val">, грн</span>
                                        </label>
                                        <span class="summ-inputs">
                                            <input value="" name="price_from" type="number" class="form-control"
                                                   id="amount-one"
                                                   placeholder="10 000">
                                            <input value="" name="price_to" type="number" class="form-control"
                                                   id="amount-two"
                                                   placeholder="70 000">
                                        </span>
                                        <label for="exampleInputName2" class="label-hrn">&#8372;</label>

                                    </div>
                                </div>
                            </div>

                            <div id="slider-range"></div>


                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-12 year-block no-pad">
                            <div class="years-filter">
                                <label class="visible-xs visible-sm" for="">Рік</label>                                
                                    
                                <select title="Рік"
                                        class="selectpicker show-menu-arrow form-control" name="year[]"
                                        multiple="multiple">

                                    @php
                                        $years = $years->toArray();
                                        krsort($years);
                                    @endphp

                                    @foreach( $years as $year => $val)
                                        <option
                                                @if(isset($_POST['year']))
                                                    @foreach( $_POST['year'] as $key => $value)
                                                        @if($year == $value)
                                                            selected="selected"
                                                        @endif
                                                    @endforeach
                                                @endif
                                                value="{{ $year }}">{{ $year }}
                                        </option>
                                    @endforeach
                                </select>

                                <input type="hidden" name="filter" value="yes">

                            </div>

                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-12 filter-btn-block no-pad">

                            <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12 no-pad">
                                <input class="filter-button" type="submit" value="Пошук">
                            </div>

                            <div class="col-lg-4 col-md-4 col-sm-0 col-xs-0" style="padding: 0px">
                                <a role="button" href="{{ url('/') }}" id="filter-button-reset" class="filter-button">&#935;</a>
                            </div>


                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-lg-12 col-md-12 col-sm-12 col-lg-12 arrow-down">
            <span class="glyphicon glyphicon-menu-up control-arrow downer hide"></span>
            <span class="glyphicon glyphicon-menu-down control-arrow upper"></span>
        </div>

    </div>
</div>
