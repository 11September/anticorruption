<?php

namespace TCG\Voyager\Widgets;

use Arrilot\Widgets\AbstractWidget;
use Illuminate\Support\Str;
use TCG\Voyager\Facades\Voyager;

class PageDimmer extends AbstractWidget
{
    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Treat this method as a controller action.
     * Return view() or other content to display.
     */
    public function run()
    {
        $count = \App\Object::count();
        //$string = trans_choice('voyager.dimmer.page', $count);

        return view('voyager::dimmer', array_merge($this->config, [
            'icon'   => 'voyager-company',
            'title'  => $count . " Об'єктів",
            'text'   => "Ведеться облік над $count об'єктами",
            'button' => [
                'text' => 'Об\'єкти',
                'link' => route('voyager.objects.index'),
            ],
            'image' => asset('/img/voyagerDimmer/objects-background-img.jpg'),
        ]));
    }
}