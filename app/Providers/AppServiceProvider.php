<?php

namespace App\Providers;

use App\Http\Middleware\EnsureTwoFactorIsVerified;
use App\Services\MenuService;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;

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
        /** @var Router $router */
        $router = $this->app['router'];
        $router->aliasMiddleware('twofactor', EnsureTwoFactorIsVerified::class);

        $this->app['events']->listen(BuildingMenu::class, function (BuildingMenu $event) {
            $menus = MenuService::getAllowedMenus();
            $event->menu->add(...$menus);
        });
    }
}
