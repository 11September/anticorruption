@extends('partials.master')

@section('css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/css/bootstrap-select.min.css"
          rel="stylesheet">
@endsection

@section('title'){{ Voyager::setting('title') }}@endsection
@section('description'){{ Voyager::setting('description') }}@endsection

@section('facebookCommentsModerator')

{{--    <meta property="og:url" content="{{ env('APP_URL') .'get-object-facebook/'. $object->id }}" /> --}}
    @if( isset( $facebookObject ) )
        <meta property="og:url" content="{{ env('APP_URL') .'$facebookObject->id/' }}" />
        <meta property="og:title" content="{{ $facebookObject->name . " - " . $facebookObject->address }}" />
        <meta property="og:description" content="{{ $facebookObject->description }}" />
        <meta property="og:image" content="https://www.facebook.com/images/fb_icon_325x325.png" />
        <meta property="og:type" content="website" />
        <meta property="fb:app_id" content="1509381815774111"/> 
    @endif

@endsection

@section('content')

    @include('partials.nav')

    <div id="left-sidebar" class="col-lg-30  sidebar sidebar-close slide-down">
        @include('partials.sidebar')
    </div>

    <div class="col-lg-12 col-md-12 col-md-12  no-pad">
        <div id="map" class="map-body">
        </div>
    </div>


    @include('partials.modalExportObjects')
    {{--@include('partials.modalRegisterDisquis')--}}


@endsection

@section('scripts')

    <script defer
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCylzj30nQuaMwhN6Xeqf7wrSYV7KR0yFs&language=uk&callback=initMap">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OverlappingMarkerSpiderfier/1.0.3/oms.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/js/bootstrap-select.min.js"></script>
    <script defer src="{{ URL::asset('js/markers_with_label.js') }}" type="text/javascript"></script>
    @include('partials.autocomplete')

    <style>
        .labels {
            text-align: center;
            width: 40px;
            height: 50px;
            /*margin-left: -20px !important;
            margin-top: -50px !important;
            padding-top: 9px*/;
            font-size: 14px;
            font-weight: bold;
            color: dimgrey;
        }
    </style>

    <script>

        var pathToImages = "{{ asset('storage') }}";        

        var map;
        var objects = {!! $objects !!};
        var regionContainsObjectsAmount = {!! $regionContainsObjectsAmount !!};
        var moneysAmount = {!! $suma !!};
        var filteredByCity = {!! $filteredByCity !!};

        function initMap() {

            var mapZoom = 6;
            var mapCenter = {lat: 49.03806488, lng: 31.4511323};
            
            var markers, locations = [], center, infoWindows = [], local_markers = [];

            for (var j = 0; j < objects.length; j++) {
                locations.push({
                    lat: parseFloat(objects[j].maps_lat),
                    lng: parseFloat(objects[j].maps_lng)
                });
            }

            if (objects.length == 1) {
                mapZoom = 18;
                mapCenter = new google.maps.LatLng(objects[0].maps_lat, objects[0].maps_lng);
                loadObjectInformation(objects[0].id);
            }

            map = new google.maps.Map(document.getElementById('map'), {
                zoom: mapZoom,
                center: mapCenter,
                zoomControl: true,
                mapTypeControl: false,
                fullscreenControl: false,
                rotateControl: false,
                streetViewControl: false
            });

            var oms = new OverlappingMarkerSpiderfier(map, {
                markersWontMove: true,
                markersWontHide: true,
                basicFormatEvents: true
            });

            if(navigator.geolocation){
                navigator.geolocation.getCurrentPosition(function(position) {
                    var pos = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };

                    map.setCenter(pos);
                    map.setZoom(15);
                });
            }

            markers = locations.map(function (location, i) {
                var objectImage = objects[i].category ? objects[i].category.image : "{{ asset('img/markers/cluster.png') }}";
                // var objectImage = "{{ asset('img/markers/marker1.png') }}";

                var image = new google.maps.MarkerImage(
                    pathToImages + "/" + objectImage,
                    // objectImage,
                    null,
                    null,
                    null,
                    new google.maps.Size(50, 50)
                );

                var marker = new google.maps.Marker({
                    position: location,
                    icon: image,
                    name: objects[i].name,
                    address: objects[i].address,
                    objectId: objects[i].id,
                    cluster: false,
                });

                google.maps.event.addListener(marker, 'spider_click', function () {

                    var object_id = marker.objectId;

                    loadObjectInformation(object_id);

                    $(".objects-table").css("display", '');
                    $(".sidebar-controller").addClass("slide-width");
                    $(".arrow-left").addClass("hide");
                    $(".arrow-right").removeClass("hide");
                    $(".sidebar-controller").toggleClass("slide-width");

                    $(".sidebar-body").removeClass("height-69");
                    $("#scroll-part").removeClass("zero-height");
                    $("#left-sidebar").removeClass("slide-down");

                    $(".down-obj").addClass("hide");
                    $(".up-obj").removeClass("hide");

                });

                return marker;
            });

            var markersInCluster = [];
            var clusterMarkers = [];
            var markerBounds;
            var clusterBounds;

            var overlay = new google.maps.OverlayView();
            overlay.draw = function () {
            };
            overlay.setMap(map);

            function createCluster(_position, _containsArray, _containsLabel){

                var clusterImagePath = "{{ asset('img/markers/cluster.png') }}";
                
                var cluster = new MarkerWithLabel({
                    position: _position,
                    map: map,
                    contains: _containsArray,
                    labelClass: "labels",
                    labelContent: _containsLabel,
                    labelInBackground: false,
                    cluster: true,
                });

                setClusterSize( cluster );

                return cluster;

            }

            function setClusterSize( cluster ){
                var size, anchor;
                
                if( cluster.labelContent >= 10000 ){
                    size = new google.maps.Size(78, 78);
                    anchor = new google.maps.Point(20, 57);
                }else if( cluster.labelContent >= 1000 ){
                    size = new google.maps.Size(65, 65);
                    anchor = new google.maps.Point(21, 50);
                }else if( cluster.labelContent >= 100 ) {
                    size = new google.maps.Size(58, 58);
                    anchor = new google.maps.Point(20, 46);
                }else{
                    size = new google.maps.Size(50, 50);
                    anchor = new google.maps.Point(20, 41);
                }

                var clusterImagePath = "{{ asset('img/markers/cluster.png') }}";

                var clusterImage = new google.maps.MarkerImage(
                    clusterImagePath,
                    null,
                    null,
                    null,
                    size
                );

                cluster.setIcon( clusterImage );
                cluster.labelAnchor = anchor;
            }

            function resetMarkers(){
                $.each( clusterMarkers, function (i, cluster) {
                    cluster.setMap(null);
                });
                $.each( markers, function (i, marker) {
                    oms.addMarker(marker);
                });
            }

            function clusterization( _mapBounds ) {
                var nextmarker;

                $.each(clusterMarkers, function( i, clus ) {
                    clus.setMap(null);
                });

                markersInCluster = [];
                clusterMarkers = [];
                markerBounds = null;
                clusterBounds = null;

                var clusterBounds = new google.maps.LatLngBounds();

                var projection = overlay.getProjection();

                function createAnglePoints(_marker) {

                    var tr = new google.maps.LatLng(_marker.getPosition().lat(), _marker.getPosition().lng());
                    var bl = new google.maps.LatLng(_marker.getPosition().lat(), _marker.getPosition().lng());

                    var trPix = projection.fromLatLngToDivPixel(tr);
                    trPix.x += 60;
                    trPix.y -= 60;

                    var blPix = projection.fromLatLngToDivPixel(bl);
                    blPix.x -= 60;
                    blPix.y += 60;

                    var ne = projection.fromDivPixelToLatLng(trPix);
                    var sw = projection.fromDivPixelToLatLng(blPix);

                    var nesw = [sw, ne];

                    return nesw;
                }

                function isMarkerInBounds( _cluster, _marker ) {
                    var nesw = createAnglePoints( _cluster );
                    var clusterBounds = new google.maps.LatLngBounds(nesw[0], nesw[1]);

                    return clusterBounds.contains( _marker.getPosition() );

                }

                function distanceBetweenPoints(p1, p2) {
                    if (!p1 || !p2) {
                        return 0;
                    }

                    var R = 6371; // Radius of the Earth in km
                    var dLat = (p2.lat() - p1.lat()) * Math.PI / 180;
                    var dLon = (p2.lng() - p1.lng()) * Math.PI / 180;
                    var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                    Math.cos(p1.lat() * Math.PI / 180) * Math.cos(p2.lat() * Math.PI / 180) *
                    Math.sin(dLon / 2) * Math.sin(dLon / 2);
                    var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                    var d = R * c;
                    return d;
                };

                function addMarkerToCluster( _marker ) {
                    var clusterToAdd;
                    var contains = [];
                    var distance = 40000;

                    for(var i = 0, cluster; cluster = clusterMarkers[i]; i++){

                        if( isMarkerInBounds( cluster, _marker) && _marker.cluster === false ){
                            var d = distanceBetweenPoints( cluster.getPosition(), _marker.getPosition() );
                            if( d < distance ){
                                distance = d;
                                clusterToAdd = cluster;
                            }
                        }

                    }

                    if( clusterToAdd ){
                        if( _marker.cluster === false ){
                            clusterToAdd.contains.push( _marker );
                            clusterToAdd.labelContent = clusterToAdd.contains.length;
                            _marker.setMap( null );
                            _marker.cluster = clusterToAdd;
                            setClusterSize( clusterToAdd );
                        }
                    }else{
                        if( _marker.cluster === false ){
                            contains.push( _marker );
                            var cluster = createCluster( marker.getPosition(), contains, contains.length );
                            cluster.contains = contains;
                            cluster.labelContent = contains.length;
                            clusterMarkers.push( cluster );
                            _marker.setMap( null );
                            _marker.cluster = cluster;

                            google.maps.event.addListener(cluster, 'click', function () {
              
                                bounds = new google.maps.LatLngBounds();

                                var mapOldZoom = map.getZoom();

                                $.each(this.contains, function( i, marker ) {
                                    bounds.extend(marker.getPosition());
                                });
                                map.fitBounds(bounds);
                                map.setCenter(bounds.getCenter());
                                
                                if(mapOldZoom == map.getZoom()){
                                    map.setZoom(mapOldZoom + 2);
                                }

                                var mapBounds = map.getBounds();
                                clusterization( mapBounds );

                            });
                        }
                    }
                }

                function isMarkerInViewport( _marker ){
                    return _mapBounds.contains( _marker.getPosition() );
                }

                var index = 0;

                while( markers[index] ){
                    var marker = markers[index];
                    index++;
                    marker.cluster = false;
                    if( isMarkerInViewport(marker) ){

                        addMarkerToCluster( marker );

                    }
                }
                
                for (var i = 0, cluster; cluster = clusterMarkers[i]; i++) {
                    if( cluster.contains.length <= 1 ){
                        cluster.setMap( null );
                        cluster.contains[0].setMap( map );
                    }
                }
            }
            
            var regionClusters = [];

            google.maps.event.addListener(map, 'idle', function () {   
                var mapBounds = map.getBounds();
                if( map.getZoom() <= 19 ){
                    regionClusterization( mapBounds );
                }else{
                    resetMarkers();
                }
            });

            google.maps.event.addListener(map, 'zoom_changed', function (){
                if (map.getZoom() < 3) { map.setZoom(3) };
            });

            function regionClusterization( _mapBounds ) {
                $.each(regionClusters, function( i, regClus ) {
                    regClus.setMap(null);
                });

                $.each(clusterMarkers, function (i, cluster) {
                    cluster.setMap(null);
                })

                if( filteredByCity ){ 
                    clusterization( _mapBounds );
                    return;
                }

                regionClusters = [];
                var regionClustersCoords = {!! $regionClustersCoords !!};

                if (map.getZoom() < 7) {

                    $.each( regionClustersCoords, function ( i, clusLatLng ) {

                        var coords = JSON.stringify( new google.maps.LatLng( clusLatLng[0].map_lat, clusLatLng[0].map_lng ));
                        var regionContains = regionContainsObjectsAmount[i].toString();

                        var regionCluster = createCluster( JSON.parse(coords), regionContains, regionContains );

                        google.maps.event.addListener(regionCluster, 'click', function () {

                            map.setZoom(8);
                            map.setCenter(regionCluster.getPosition());

                            $.each(regionClusters, function( i, regClus ) {
                                regClus.setMap(null);
                            });

                            clusterization( _mapBounds );

                        });

                        regionClusters.push(regionCluster);
                    });
                } else {
                    clusterization( _mapBounds );
                }
            }


        }

        function hideInfo() {
            $('#object-one-list').empty();
            $('#comments-part').empty();
            $('#object-all-list').empty();
            $(".selected-object-label").addClass("hide");
            $('.list-objects-label').removeClass("hide");
            var moneyPrefix = '', moneysAmountFormatted;
            if(moneysAmount < 1000000){
                moneysAmountFormatted = parseInt(moneysAmount / 1000);
                moneyPrefix = ' тис.';
            }else if(moneysAmount < 1000000000){
                moneysAmountFormatted = parseInt(moneysAmount / 1000000);
                moneyPrefix = ' млн.';
            }else if(moneysAmount < 1000000000000){
                moneysAmountFormatted = parseInt(moneysAmount / 1000000000);
                moneyPrefix = ' млрд.';
            }else { 
                moneysAmountFormatted = 'Більше трильйона';
            }

            $(".money-indicator").text(moneysAmountFormatted + moneyPrefix);

            $('.pagination-links-div').removeClass('hide');

            sidebarObjectsAmount($('.disabled-link').text());
        }

        function sidebarObjectsAmount(amount) {
            $("#object-all-list").empty();

            if (amount > objects.length) {
                amount = objects.length;
            }

            if (amount < 10) {
                $('.pagination-links-div').addClass('hide');
            }

            for (var i = 0; i < amount; i++) {

                var formattedPrice = parseInt(objects[i].price / 1000);
                var objectImage = objects[i].category.image ? objects[i].category.image : "{{ asset('img/markers/cluster.png') }}";

                $("#object-all-list").append(
                    '<span class="list-item-flex">' +
                    '<input class="hiidenIdObject" type="hidden" value=' + objects[i].id + '>' +
                    '<img class="image" src="' + pathToImages + "/" + objects[i].category.image +'" alt="">' +
                    '<div class="object-info">' +
                    '<span class="object-info-p">' + objects[i].name + '</span>' +
                    '<span class="object-info-p">' + objects[i].address + '</span>' +
                    '<span class="object-info-p">Сплачено ' + formattedPrice + ' тис. грн.</span>' +
                    '<span class="hide lat">' + objects[i].maps_lat + '</span>' +
                    '<span class="hide lng">' + objects[i].maps_lng + '</span>' +
                    '</div>' +
                    '</span>'
                );
            }
        }

        function loadObjectInformation(object_id) {
            $('.pagination-links-div').addClass('hide');

            $.ajax({
                type: 'GET',
                url: '/get-object',
                data: {id: object_id},
                success: function (object) {

                    var time, comment, name;

                    $('#object-one-list').empty();
                    $('#comments-part').empty();
                    $('#object-all-list').empty();

                    $(".money-indicator").text(parseInt(object[0].price / 1000) + ' тис.');

                    if ('name' in object[0]) {
                        $('#object-one-list').append(
                            '<li>' +
                            '<p class="object-name">Назва</p>' +
                            '<p class="object-description">' + object[0].name + '</p>' +
                            '</li>'
                        );
                    }

                    if ('address' in object[0]) {
                        $('#object-one-list').append(
                            '<li>' +
                            '<p class="object-name">Адреса</p>' +
                            '<p id="object-adress" class="object-description">' + object[0].address + '</p>' +
                            '</li>'
                        );
                    }
                    if (object[0]['customer'] !== null) {
                        if ('name' in object[0]['customer']) {
                            $('#object-one-list').append(
                                '<li>' +
                                '<p class="object-name">Замовник</p>' +
                                '<p id="object-customer" class="object-description">' + object[0]['customer'].name + '</p>' +
                                '</li>'
                            );
                        }
                    }

                    if (object[0]['contractor'] !== null) {
                        if ('name' in object[0]['contractor']) {
                            $('#object-one-list').append(
                                '<li>' +
                                '<p class="object-name">Підрядник</p>' +
                                '<p id="object-contractor" class="object-description">' + object[0]['contractor'].name + '</p>' +
                                '</li>'
                            );
                        }
                    }

                    if ('work_description' in object[0]) {
                        $('#object-one-list').append(
                            '<li class="work-description-block">' +
                            '<p class="object-name">Перелік робіт</p>' +
                            '<p id="object-work-description" class="object-description work-description">' + object[0].work_description + '</p>' +
                            '</li>'
                        );
                    }

                    if (object[0].documents.length > 0) {

                        $('#object-one-list').append('<li class="document-part"><p class="object-name">Додаткові документи</p></li>');

                        $.each(object[0].documents, function( i, objectDoc ) {

                            $('.document-part').append(
                                '<a target="_blank" href="' + objectDoc.file_path + '">' + objectDoc.title + '</a>'
                            );

                        });
                    }
                    var objectSum = 0;
                    var status = 'Сплачено';

                    $('#object-one-list').append('<li class="finances-part"><p class="object-name">Фiнансування</p></li>');

                    if( 'finances' in object[0] && object[0].finances.length > 0) {
                        $.each(object[0].finances, function( i, objectFin ) {
                        
                            objectSum += objectFin.suma;
                        
                            if(objectFin.status == "paid"){
                                status = 'Сплачено';
                            }
                            else if (objectFin.status == "provided") {
                                status = 'Передбачено';
                            }
                            else {
                            }
                            $('.finances-part').append(
                                '<p class="object-description">' + objectSum + ' грн. ' + status + '</p>'
                            );
                            if (objectFin.description) {
                                $('.finances-part').append(
                                    '<p class="object-description">' + objectFin.description + '</p>'
                                );
                            }
                        });
                    } else if( 'price' in  object[0] ) {

                        $('.finances-part').append(
                            '<p class="object-description">' + object[0].price + ' грн. ' + status + '</p>'
                        );

                    }else{
                        $('.finances-part').append(
                            '<p class="object-description">Не має прайсингу для даного об’єкта</p>'
                        );
                    }

                    $('#object-one-list').append(
                        '<input class="hiidenIdObject" type="hidden" value="' + object[0].id + '">'
                    );

                    $('.selected-object-head').text("До об’єктів");

                    var commentsFacebook =
                    '<div class="fb-comments" data-href="https://map.shtab.net/get-object-facebook/' + object[0].id + '" data-numposts="5" data-width="250"></div>';

                    $('#comments-part').append(
                        '<p class="comments-head">Залишити коментар</p>' +
                        '<div class="facebook-comments-wrapper">' +
                            commentsFacebook +
                        '</div>'
                    );

                    FB.XFBML.parse();
                }
            });

            $(".selected-object-label").removeClass("hide");
            $('.list-objects-label').addClass("hide");
        }

        $(document).ready(function () {

            $("#pagination-ten").on("click", function () {
                $("#pagination-ten").addClass("disabled-link");
                $("#pagination-twenty").removeClass("disabled-link");
                $("#pagination-fifty").removeClass("disabled-link");
            });
            $("#pagination-twenty").on("click", function () {
                $("#pagination-ten").removeClass("disabled-link");
                $("#pagination-twenty").addClass("disabled-link");
                $("#pagination-fifty").removeClass("disabled-link");
            });
            $("#pagination-fifty").on("click", function () {
                $("#pagination-ten").removeClass("disabled-link");
                $("#pagination-twenty").removeClass("disabled-link");
                $("#pagination-fifty").addClass("disabled-link");
            });

            $('.selectpicker').selectpicker({
                showIcon: true
            });

            $("#object-all-list").on("click", ".list-item-flex", function () {
                var object_lat = parseFloat($(this).find($('.lat')).text());
                var object_lng = parseFloat($(this).find($('.lng')).text());
                var object_id = ($(this).find($('.hiidenIdObject')).val());

                if( !isNaN(object_lat) || !isNaN(object_lng) ) {

                    var LatLng = new google.maps.LatLng(object_lat, object_lng);
                    map.setCenter(LatLng);
                    map.setZoom(18);
                }

                loadObjectInformation(object_id);
            });

            $(".objects-table").css({width: 'toggle'});

            $(".sidebar-controller").click(function () {
                $(".objects-table").animate({width: 'toggle'});
                $(".sidebar-controller").toggleClass("slide-width");
                $(".arrow-right").toggleClass("hide");
                $(".arrow-left").toggleClass("hide");
            });

            $(".mobile-sidebar-controller").click(function () {
                $(".sidebar").toggleClass("slide-down");

                $(".sidebar-body").toggleClass("height-69");
                $(".scroll-part").toggleClass("zero-height");

                $(".down-obj").toggleClass("hide");
                $(".up-obj").toggleClass("hide");
            });

            $(".control-arrow").click(function () {
                $(".search-body").slideToggle();

                $(".search-body").removeClass('hide')


                $(".downer").toggleClass("hide");
                $(".upper").toggleClass("hide");
            });

            $(function () {
                $("#slider-range").slider({
                    range: true,
                    min: 0,
                    max: 1000000,
                    values: [{{ isset($_POST['price_from']) ? $_POST['price_from'] : 0 }},{{ isset($_POST['price_to']) ? $_POST['price_to'] : 700000 }}],
                    slide: function (event, ui) {
                        $("#amount-one").val(ui.values[0]);
                        $("#amount-two").val(ui.values[1]);
                    }
                });
                $("#amount-one").val($("#slider-range").slider("values", 0));
                $("#amount-two").val($("#slider-range").slider("values", 1));
            });

            $.each($(".single-select"), function ( i, that ) {

                if( $(that).find("li.selected").length > 0 ){
                    $(that).find("button").addClass('has-selected');
                }else{
                    if( $(that).find("button").hasClass('has-selected') ){
                        $(that).find("button").removeClass('has-selected');
                    }
                }
            });

            

            $(".single-select").on('change', 'select', function() {

                if( $(this).parent().find("li.selected").length > 0 ){
                    $(this).parent().find("button").addClass('has-selected');
                }else{
                    if( $(this).parent().find("button").hasClass('has-selected') ){
                        $(this).parent().find("button").removeClass('has-selected');

                    }
                }

            });
            
        });




        function selectAll(source) {
            checkboxes = document.getElementsByName('save[]');
            for (var i = 0, n = checkboxes.length; i < n; i++) {
                checkboxes[i].checked = source.checked;
            }
        }

        $( ".exp-var" ).click(function( event ) {
            var selected = [];

            $('#errorDiv').empty();

            $('#checkboxesWrapper input:checked').each(function() {
                selected.push($(this).attr('value'));
            });

            if( selected.length == 0 ){
                event.preventDefault();
                $( "#errorDiv" ).append( '<p class="export-error">Будь ласка, оберіть об’єкти</p>' );
            }
        });

    </script>
@endsection
