<?php

namespace App\Providers;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;
use App\Services\MenuService;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app['events']->listen(BuildingMenu::class, function (BuildingMenu $event) {
            $menus = MenuService::getAllowedMenus();
            $event->menu->add(...$menus);
        });
    }
}
