<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use App\Page;
use App\City;
use App\Contractor;
use App\Customer;
use App\Object;
use App\ObjectCategory;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        view()->composer('partials.nav', function ($view){
//            $view->with('categories', ObjectCategory::categories());
            $view->with('categories', ObjectCategory::allCategories());
//            $view->with('cities', City::cities());
            $view->with('cities', City::allCities());
            $view->with('customers', Customer::customers());
            $view->with('contractors', Contractor::contractors());
            $view->with('years', Object::yearsInterval());
        });

        view()->composer('partials.autocomplete', function ($view){
            $view->with('addresses', Object::addresses());
        });
    }
}
