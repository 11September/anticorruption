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
                                                                           data-toggle="tab">Для чого потрібна карта?</a></li>
                        <li role="presentation " class="handler"><a href="#2-tab" class="tab-link"
                                                                    aria-controls="profile" role="tab"
                                                                    data-toggle="tab">Як користуватися картою? </a></li>
                        <li role="presentation" class="handler"><a href="#3-tab" class="tab-link"
                                                                   aria-controls="messages" role="tab"
                                                                   data-toggle="tab">Як знайти потрібну адресу на карті?</a></li>
                        <li role="presentation" class="handler"><a href="#4-tab" class="tab-link"
                                                                   aria-controls="settings" role="tab"
                                                                   data-toggle="tab">Як залишити скаргу або повідомлення про імовірну проблему з об’єктом?</a></li>

                        <li role="presentation" class="handler"><a href="#5-tab" class="tab-link" aria-controls="home"
                                                                   role="tab" data-toggle="tab">Як дізнатися, що мені відповіли?</a></li>
                    </ul>
                </div>

                <div class="col-lg-10 col-md-9 col-sm-9 col-xs-8 tab-content-block">
                    <span class="glyphicon glyphicon-menu-left back-to-links"></span>
                    <div class="instruction api">
                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="welcome">
                                <h1>Для чого потрібна карта?</h1>
                                <p>Щороку з міських бюджетів виділяють мільйони гривень на ремонти житлових будинків і соціальних закладів: утеплити ваш будинок, пофарбувати майданчик, де бавиться ваша дитина, або замінити вікна у найближчій лікарні. 
                                    Деякі ремонтні роботи виконуються, а деякі - ні. Але зазвичай кошти, виділені з бюджету, списуються у будь-якому випадку. Саме для того, щоб кожен українець зміг легко контролювати виділені кошти та виконання ремонтних робіт, ми створили АНТИКОРУПЦІЙНУ КАРТУ РЕМОНТІВ.
                                </p>
                                <blockquote>
                                    <p>http://map.shtab.net/api/objects/?address=Київ%2C+вул.+Тампере%2C+8</p>
                                </blockquote>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="2-tab">
                                <h1>Як користуватися картою? </h1>
                                <p>Карта має велику кількість можливостей, які роблять користування нею значно зручнішим.
                                    По-перше, тут можна знайти будівлю, яка вас цікавить, і перевірити чи виділялись на її ремонт кошти. Для цього скористайтесь пошуком по карті в лівому верхньому кутку або вручну збільшіть карту в тому місці, яке вас цікавить.
                                    По-друге, можна залишити коментар по будь-якому об’єкту, якщо вам відома про нього певна інформація (наприклад, про те, що ремонту не було або ж навпаки). Більше того, ви можете прикріпити фото до свого коментаря. Також ви можете побачити чи залишали коментарі з приводу цього об'єкту інші відвідувачі сайту та за бажанням зв'язатись з ними. 
                                    По-третє, можна скористатись фільтром, якщо ви ви хочете звузити пошук і вивчити лише ті об'єкти, які виконувались певним підрядником, будівлі, ремонт яких коштував понад мільйон гривень і так далі. Для цього скористайтесь фільтром зверху. Зокрема, у фільтрі ви можете обирати категорію об'єктів, місто, замовника, підрядника, суму виконаних робіт та/або рік. 
                                </p>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="3-tab">
                                <h1>Як знайти потрібну адресу на карті?</h1>
                                <p>Все досить просто. По-перше, ви можете дозволити карті бачити вашу геолокацію (з ноутбука чи телефона) і тоді сайт автоматично покаже вам об'єкти поблизу вашого поточного місця перебування. Тож ви одразу зможете подивитися, які роботи повинні були бути відремонтованими навколо місця, де ви зараз перебуваєте.
                                По-друге, якщо вас цікавить конкретна вулиця, ви можете скористатися пошуком за її назвою. Для цього введіть назву потрібної вам вулиці в полі “знайти на карті” у верхньому лівому куті. Під час введення, система запропонує всі доступні варіанти у випадаючому списку, вам потрібно буде обрати правильний і натиснути “пошук”.
                                </p>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="4-tab">
                                <h1>Як залишити скаргу або повідомлення про імовірну проблему з об’єктом?</h1>
                                <p>Для цього зайдіть на потрібний об’єкт і залиште свій коментар, авторизувавшись через Facebook. Ви можете додати фото до свого коменаря за необхідності.
                                Якщо у вас немає профілю у Facebook, ви можете надіслати нам усю інформацію на пошту - shtab.net [at] gmail.com або скористатись іншим зручним для вас способом зв'язку. Для цього скористайтесь вкладкою “контакти” у шапці сайту.
                                </p>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="5-tab">
                                <h1>Як дізнатися, що мені відповіли?</h1>
                                <p>Якщо ви надіслали нам коментар, то Facebook вас сповістить, коли ми на нього дамо відповідь.
                                    Якщо ви надіслали електронного листа, будь ласка, перевіряйте свою електронну пошту. 
                                    Ми обіцяємо відповідати протягом 48 годин.
                                    В принципі, якщо ми можемо самостійно запостити цей текст, то давайте так і зробимо. Від вас тоді потрібно змінити назви рубрик в шапці
                                    І також зараз при натисканні на ці рубрики видає код-помилку
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