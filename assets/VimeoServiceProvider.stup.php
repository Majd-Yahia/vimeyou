<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Nurinteractive\WhatsappCloudApi\WhatsappCloudApi;
use Nwidart\Modules\Facades\Module;

class VimeoServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/routes/api.php');

        /*
         * Optional methods to load your package assets
         */
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'whatsapp-cloud-api');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'whatsapp-cloud-api');

        if (self::shouldRunMigrations()) {

            if (self::shouldPublishMigrateToModules()) {
                $this->publishMigrationsThroghModule();
            } else {
                $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');
            }
        }


        if ($this->app->runningInConsole()) {

            //PUBLISH THE CONFIG FILE
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('vimeo.php'),
            ], 'config');

            //PUBLISH THE SERVICES PROVIDER
            $this->publishes([
                __DIR__ . '/../assets/VimeoServiceProvider.stup.php' => app_path('Providers/VimeoServiceProvider.php'),
            ], 'providers');

            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/whatsapp-cloud-api'),
            ], 'views');*/

            // Publishing assets.
            /*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/whatsapp-cloud-api'),
            ], 'assets');*/

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/whatsapp-cloud-api'),
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
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'vimeo');

        // Register the main class to use with the facade
        // $this->app->singleton('whatsapp-cloud-api', function () {
        //     return new WhatsappCloudApi;
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
        // $module = config('vimeo.migrations.modules.module_name');
        // $module = Module::find($module);
        // $this->publishes([
        //     __DIR__ . '/Database/Migrations' => $module->getExtraPath(config('modules.paths.generator.migration.path')),
        // ], 'whatsapp-migrations');
    }
}
