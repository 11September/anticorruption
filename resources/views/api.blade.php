@extends('partials.master')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/font-awesome.min.css') }}">
@endsection

@section('content')
    <div class="api-section">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-2 col-md-3 col-sm-3 col-xs-4 tab-links-block">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="handler active "><a href="#welcome" class="tab-link"
                                                                           aria-controls="home" role="tab"
                                                                           data-toggle="tab">Знакомство с API</a></li>
                        <li role="presentation " class="handler"><a href="#2-tab" class="tab-link"
                                                                    aria-controls="profile" role="tab"
                                                                    data-toggle="tab">Всі регіони</a></li>
                        <li role="presentation" class="handler"><a href="#3-tab" class="tab-link"
                                                                   aria-controls="messages" role="tab"
                                                                   data-toggle="tab">Об'єкти за регіоном</a></li>
                        <li role="presentation" class="handler"><a href="#4-tab" class="tab-link"
                                                                   aria-controls="settings" role="tab"
                                                                   data-toggle="tab">Об'єкти за типом</a></li>

                        <li role="presentation" class="handler"><a href="#5-tab" class="tab-link" aria-controls="home"
                                                                   role="tab" data-toggle="tab">Окремий об'єкт</a></li>
                        <li role="presentation " class="handler"><a href="#6-tab" class="tab-link"
                                                                    aria-controls="profile" role="tab"
                                                                    data-toggle="tab">Об'єкти за регіоном</a></li>
                        <li role="presentation" class="handler"><a href="#7-tab" class="tab-link"
                                                                   aria-controls="messages" role="tab"
                                                                   data-toggle="tab">Об'єкти за типом</a></li>
                    </ul>
                </div>

                <div class="col-lg-10 col-md-9 col-sm-9 col-xs-8 tab-content-block">
                    <span class="glyphicon glyphicon-menu-left back-to-links"></span>
                    <div class="instruction api">
                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="welcome">
                                <h1>API</h1>
                                <p>В этом руководстве Вы найдете базовую информацию о принципах работы API и о
                                    подготовке к его использованию. Если Вы уже работали с нашим API или с аналогичными
                                    сервисами других платформ, и знаете, какое приложение хотите создать, мы рекомендуем
                                    Вам перейти в соответствующий раздел документации.

                                    API (application programming interface) — это посредник между разработчиком
                                    приложений и какой-либо средой, с которой это приложение должно взаимодействовать.
                                    API упрощает создание кода, поскольку предоставляет набор готовых классов, функций
                                    или структур для работы с имеющимися данными.
                                </p>

                                <p>API — это интерфейс, который позволяет получать информацию из базы данных shtab с
                                    помощью http-запросов к специальному серверу. Вам не нужно знать в подробностях, как
                                    устроена база, из каких таблиц и полей каких типов она состоит — достаточно того,
                                    что API-запрос об этом «знает». Синтаксис запросов и тип возвращаемых ими данных
                                    строго определены на стороне самого сервиса.

                                    Например, для получения данных о обьекте с идентификатором с адресом "Київ, вул.
                                    Тампере, 8" необходимо составить запрос такого вида:
                                </p>

                                <blockquote>
                                    <p>http://map.shtab.net/api/objects/?address=Київ%2C+вул.+Тампере%2C+8</p>
                                </blockquote>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="2-tab">
                                <h1>TABS 2</h1>
                                <p>Wahoo turbot parasitic catfish long-whiskered catfish whale shark silverside longfin
                                    smelt wolffish mustache triggerfish rock bass. Largemouth bass Gila trout
                                    mail-cheeked
                                    fish. Roach; Blacksmelt bocaccio saw shark barb--Long-finned sand diver bleak glass
                                    catfish.
                                </p>

                                <p>Wahoo turbot parasitic catfish long-whiskered catfish whale shark silverside longfin
                                    smelt wolffish mustache triggerfish rock bass. Largemouth bass Gila trout
                                    mail-cheeked
                                    fish. Roach; Blacksmelt bocaccio saw shark barb--Long-finned sand diver bleak glass
                                    catfish.
                                </p>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="3-tab">
                                <h1>TABS 3</h1>
                                <p>Wahoo turbot parasitic catfish long-whiskered catfish whale shark silverside longfin
                                    smelt wolffish mustache triggerfish rock bass. Largemouth bass Gila trout
                                    mail-cheeked
                                    fish. Roach; Blacksmelt bocaccio saw shark barb--Long-finned sand diver bleak glass
                                    catfish.
                                </p>

                                <p>Wahoo turbot parasitic catfish long-whiskered catfish whale shark silverside longfin
                                    smelt wolffish mustache triggerfish rock bass. Largemouth bass Gila trout
                                    mail-cheeked
                                    fish. Roach; Blacksmelt bocaccio saw shark barb--Long-finned sand diver bleak glass
                                    catfish.
                                </p>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="4-tab">
                                <h1>TABS 4</h1>
                                <p>Wahoo turbot parasitic catfish long-whiskered catfish whale shark silverside longfin
                                    smelt wolffish mustache triggerfish rock bass. Largemouth bass Gila trout
                                    mail-cheeked
                                    fish. Roach; Blacksmelt bocaccio saw shark barb--Long-finned sand diver bleak glass
                                    catfish.
                                </p>

                                <p>Wahoo turbot parasitic catfish long-whiskered catfish whale shark silverside longfin
                                    smelt wolffish mustache triggerfish rock bass. Largemouth bass Gila trout
                                    mail-cheeked
                                    fish. Roach; Blacksmelt bocaccio saw shark barb--Long-finned sand diver bleak glass
                                    catfish.
                                </p>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="5-tab">
                                <h1>TABS 5</h1>
                                <p>Wahoo turbot parasitic catfish long-whiskered catfish whale shark silverside longfin
                                    smelt wolffish mustache triggerfish rock bass. Largemouth bass Gila trout
                                    mail-cheeked
                                    fish. Roach; Blacksmelt bocaccio saw shark barb--Long-finned sand diver bleak glass
                                    catfish.
                                </p>

                                <p>Wahoo turbot parasitic catfish long-whiskered catfish whale shark silverside longfin
                                    smelt wolffish mustache triggerfish rock bass. Largemouth bass Gila trout
                                    mail-cheeked
                                    fish. Roach; Blacksmelt bocaccio saw shark barb--Long-finned sand diver bleak glass
                                    catfish.
                                </p>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="6-tab">
                                <h1>TABS 6</h1>
                                <p>Wahoo turbot parasitic catfish long-whiskered catfish whale shark silverside longfin
                                    smelt wolffish mustache triggerfish rock bass. Largemouth bass Gila trout
                                    mail-cheeked
                                    fish. Roach; Blacksmelt bocaccio saw shark barb--Long-finned sand diver bleak glass
                                    catfish.
                                </p>

                                <p>Wahoo turbot parasitic catfish long-whiskered catfish whale shark silverside longfin
                                    smelt wolffish mustache triggerfish rock bass. Largemouth bass Gila trout
                                    mail-cheeked
                                    fish. Roach; Blacksmelt bocaccio saw shark barb--Long-finned sand diver bleak glass
                                    catfish.
                                </p>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="7-tab">
                                <h1>TABS 7</h1>
                                <p>Wahoo turbot parasitic catfish long-whiskered catfish whale shark silverside longfin
                                    smelt wolffish mustache triggerfish rock bass. Largemouth bass Gila trout
                                    mail-cheeked
                                    fish. Roach; Blacksmelt bocaccio saw shark barb--Long-finned sand diver bleak glass
                                    catfish.
                                </p>

                                <p>Wahoo turbot parasitic catfish long-whiskered catfish whale shark silverside longfin
                                    smelt wolffish mustache triggerfish rock bass. Largemouth bass Gila trout
                                    mail-cheeked
                                    fish. Roach; Blacksmelt bocaccio saw shark barb--Long-finned sand diver bleak glass
                                    catfish.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    @include('partials.footer')
@endsection



@section('scripts')
    <script>
        $('#myTabs a').click(function (e) {
            e.preventDefault()
            $(this).tab('show')
        });


        if (window.innerWidth < 480) {
            $(".nav-tabs li").click(function () {
                $(".nav-tabs").fadeOut();
                $(".tab-content-block").fadeIn();
            });


            $(".back-to-links").click(function () {
                $(".nav-tabs").fadeIn();
                $(".tab-content-block").fadeOut();
            });
        }


    </script>
@endsection