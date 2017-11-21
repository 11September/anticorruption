<header>
    <nav class="navbar">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                        data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="logo" href="{{ url('/') }}"><img src="{{ asset('img/logo.png') }}" alt=""></a>
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav mainmenu">

                    {{--@foreach($pages as $page)--}}
                        {{--<li><a href="page-{{ $page->slug }}">{{ $page->title }}</a></li>--}}
                    {{--@endforeach--}}

                    <li><a href="{{ url('/page-contacts') }}">Контакти</a></li>
                    <li><a href="{{ url('/page-instrukciya') }}">Інструкція</a></li>

                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li><a class="money-value" href="#">Витрачено <br/> коштів, грн</a></li>

                    <li><a class="money-indicator">

                            @if($suma < 1000000)
                                {{ floor($suma / 1000) . " тис." }}
                            @elseif($suma < 1000000000)
                                {{ floor($suma / 1000000) . " млн." }}
                            @elseif($suma < 1000000000000)
                                {{ floor($suma / 1000000000) . " млрд." }}
                            @else
                                Більше трильйона
                            @endif
                        </a></li>
                    <li class="sm-delete"><a href="#">(С) Антикорупційний штаб, 2017</a></li>
                    <li class="sm-delete"><a href="#">Розробка - UAT</a></li>
                </ul>
            </div>
        </div>
    </nav>
</header>
