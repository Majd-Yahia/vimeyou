<?php

namespace Awesomchu\Vimeo;

use Awesomchu\Vimeo\Core\Platform\Vimeo;
use Awesomchu\Vimeo\Services\VimeoFacade;
use Illuminate\Support\ServiceProvider;

class VimeoyouServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        // $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        // $this->loadRoutesFrom(__DIR__ . '/routes/api.php');

        /*
         * Optional methods to load your package assets
         */
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'whatsapp-cloud-api');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'whatsapp-cloud-api');

        // if (self::shouldRunMigrations()) {

        //     if (self::shouldPublishMigrateToModules()) {
        //         $this->publishMigrationsThroghModule();
        //     } else {
        //         $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');
        //     }
        // }


        if ($this->app->runningInConsole()) {

            //PUBLISH THE CONFIG FILE
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('vimeoyou.php'),
            ], 'config');

            //PUBLISH THE SERVICES PROVIDER
            // $this->publishes([
            //     __DIR__ . '/../assets/VimeoServiceProvider.stup.php' => app_path('Providers/VimeoServiceProvider.php'),
            // ], 'providers');

            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/vimeo'),
            ], 'views');*/

            // Publishing assets.
            /*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/vimeo'),
            ], 'assets');*/

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/vimeo'),
            ], 'lang');*/

            // Registering package commands.
            // $this->commands([]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'vimeoyou');

        $this->app->register(EventServiceProvider::class);

        // Register the main class to use with the facade
        $this->app->bind('vimeo', function () {
            return new VimeoFacade();
        });

        // Register the main class to use with the facade
        // $this->app->singleton('vimeo', function () {
        //     return new Vimeo;
        // });
    }

    /**
     * Determine if Sanctum's migrations should be run.
     *
     * @return bool
     */
    public static function shouldRunMigrations()
    {
        return config('vimeo.migrations.should_run_migrations');
    }

    /**
     * Determine if Sanctum's migrations should be loded throgh to tenants databases.
     *
     * @return bool
     */
    public static function shouldPublishMigrateToModules()
    {
        return config('vimeo.migrations.modules.enabled');
    }

    public function publishMigrationsThroghModule()
    {
    }
}
